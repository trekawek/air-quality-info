var gulp = require('gulp');
var concat = require('gulp-concat');
var terser = require('gulp-terser');
var rename = require('gulp-rename');
var replace = require('gulp-replace');
var cleanCSS = require('gulp-clean-css');
var merge = require('merge-stream');
var penthouse = require('penthouse');
const fs = require('fs');

var paths = {
  styles: {
    src: [
      'src/vendor/css/ubuntu-mono-font.css',
      'node_modules/font-awesome/css/font-awesome.css',
      'node_modules/weathericons/css/weather-icons.css',
      'node_modules/smartmenus/dist/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.css',
      'node_modules/leaflet/dist/leaflet.css',
      'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
      'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css',
    ],
    dest: 'src/htdocs/public/css/'
  },
  adminStyles: {
    src: [
      'src/vendor/css/ubuntu-mono-font.css',
      'node_modules/@coreui/icons/css/coreui-icons.css',
      'node_modules/flag-icon-css/css/flag-icon.css',
      'node_modules/font-awesome/css/font-awesome.css',
      'node_modules/simple-line-icons/css/simple-line-icons.css',
      'node_modules/@coreui/coreui/dist/css/coreui.css',
    ],
    dest: 'src/htdocs/admin/public/css/'
  },
  scripts: {
    src: [
      'node_modules/@googlemaps/markerclusterer/dist/index.min.js',
      'node_modules/jquery/dist/jquery.slim.js',
      'node_modules/bootstrap/dist/js/bootstrap.js',
      'node_modules/moment/moment.js',
      'node_modules/moment-timezone/builds/moment-timezone-with-data.js',
      'node_modules/moment/locale/cs.js',
      'node_modules/moment/locale/hu.js',
      'node_modules/moment/locale/nl.js',
      'node_modules/moment/locale/pl.js',
      'node_modules/moment/locale/ro.js',
      'node_modules/chart.js/dist/Chart.js',
      'node_modules/chartjs-plugin-annotation/chartjs-plugin-annotation.js',
      'node_modules/smartmenus/dist/jquery.smartmenus.js',
      'node_modules/smartmenus/dist/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.js',
      'node_modules/leaflet/dist/leaflet.js',
      'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
    ],
    dest: 'src/htdocs/public/js/'
  },
  adminScripts: {
    src: [
      'node_modules/jquery/dist/jquery.js',
      'node_modules/popper.js/dist/umd/popper.js',
      'node_modules/bootstrap/dist/js/bootstrap.js',
      'node_modules/@coreui/coreui/dist/js/coreui.js',
      'node_modules/@coreui/coreui/dist/js/coreui.js',
      'node_modules/location-picker/dist/location-picker.umd.js',
    ],
    dest: 'src/htdocs/admin/public/js/'
  },
  images: {
    src: [
      'node_modules/leaflet/dist/images/**',
    ],
    dest: 'src/htdocs/public/img/'
  },
  themes: [
    {
      name: 'default',
      src: 'node_modules/bootstrap/dist/css/bootstrap.css'
    },
    {
      name: 'darkly',
      src: 'node_modules/bootswatch/dist/darkly/bootstrap.css'
    }
  ]
};

function styles() {
  return gulp.src(paths.styles.src)
    .pipe(cleanCSS())
    .pipe(concat('vendor.min.css'))
    .pipe(replace('../fonts/fontawesome', '/public/fonts/fontawesome'))
    .pipe(replace('font/', 'fonts/'))
    .pipe(gulp.dest(paths.styles.dest));
}

function adminStyles() {
  return gulp.src(paths.adminStyles.src)
    .pipe(cleanCSS())
    .pipe(replace('../fonts/fontawesome', '/public/fonts/fontawesome'))
    .pipe(replace('node_modules/simple-line-icons/fonts/', 'admin/public/fonts/'))
    .pipe(concat('vendor.min.css'))
    .pipe(gulp.dest(paths.adminStyles.dest));
}

function bootstrapThemes() {
  var tasks = paths.themes.map(function(obj) {
    return gulp.src(obj.src)
    .pipe(cleanCSS())
    .pipe(rename({
      basename: obj.name,
      suffix: '.min'
    }))
    .pipe(gulp.dest(paths.styles.dest + "/themes"));
  });
  return merge(tasks);
}

function scripts() {
  return gulp.src(paths.scripts.src, { sourcemaps: true })
    .pipe(terser())
    .pipe(concat('vendor.min.js'))
    .pipe(gulp.dest(paths.scripts.dest));
}

function adminScripts() {
  return gulp.src(paths.adminScripts.src, { sourcemaps: true })
    .pipe(terser())
    .pipe(concat('vendor.min.js'))
    .pipe(gulp.dest(paths.adminScripts.dest));
}

function images() {
  return gulp.src(paths.images.src)
    .pipe(gulp.dest(paths.images.dest));
}

function createCriticalCss() {
  var cssString = "";
  [
    'src/htdocs/public/css/themes/default.min.css',
    'src/htdocs/public/css/vendor.min.css',
    'src/htdocs/public/css/style.css'
  ].forEach(function(path) {
    cssString += fs.readFileSync(path);
  });
  return penthouse({
    url: 'http://grunwald.aqi.eco.localhost:8080/en/',
    cssString: cssString
  })
  .then(criticalCss => {
    // use the critical css
    fs.writeFileSync('src/htdocs/public/css/critical.css', criticalCss);
  });
}

function watch() {
  gulp.watch(paths.scripts.src, scripts);
  gulp.watch(paths.styles.src, styles);
}

/*
 * Specify if tasks run in series or parallel using `gulp.series` and `gulp.parallel`
 */
var build = gulp.series(gulp.parallel(styles, adminStyles, bootstrapThemes, scripts, adminScripts, images));

/*
 * You can use CommonJS `exports` module notation to declare tasks
 */
exports.styles = styles;
exports.adminStyles = adminStyles;
exports.themes = bootstrapThemes;
exports.scripts = scripts;
exports.adminScripts = adminScripts;
exports.images = images;
exports.watch = watch;
exports.build = build;
exports.createCriticalCss = createCriticalCss;
/*
 * Define default task that can be called by just running `gulp` from cli
 */
exports.default = build;
