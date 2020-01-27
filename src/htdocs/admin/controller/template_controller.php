<?php
namespace AirQualityInfo\Admin\Controller;

class TemplateController extends AbstractController {

    private $templateModel;

    private $attachmentModel;

    public function __construct(
            \AirQualityInfo\Model\TemplateModel $templateModel,
            \AirQualityInfo\Model\AttachmentModel $attachmentModel) {
        $this->authorizationRequired = false;
        $this->templateModel = $templateModel;
        $this->attachmentModel = $attachmentModel;
    }

    public function edit() {
        $this->authorize();

        $template = $this->templateModel->getTemplate($this->user['id']);
        $brandIcon = $this->attachmentModel->getFileInfo($this->user['id'], 'brand_icon');
        $templateForm = new \AirQualityInfo\Lib\Form\Form('templateForm');

        if ($brandIcon !== null) {
            $templateForm->addElement('current_brand_icon', 'image', 'Current brand icon', array('style' => 'height: 3rem;'))->setValue('brand_icon');
            $templateForm->addElement('remove_brand_icon', 'checkbox', 'Remove brand icon');
            $templateForm->addElement('customize_favicon', 'checkbox', 'Use as a favicon');
        }
        $templateForm->addElement('brand_icon', 'file', 'Update brand icon (1)', array(), 'Use PNG picture')
            ->addRule('file_max_size', array('value' => 256 * 1024 * 1024, 'message' => __('Maximum allowed size is 256 kB.')))
            ->addRule('file_type', array('types' => array('image/png', 'image/svg+xml'), 'message' => __('Only PNGs and SVGs are allowed')));

        $templateForm->addElement('brand_name', 'text', 'Brand name (2)');
        $templateForm->addElement('custom_page_name', 'text', 'About page name (3)', array(), 'Fill to add a new item to the menu.');
        $templateForm->addElement('custom_page', 'textarea', 'About page body', array('rows'=>8), 'This is the HTML content of the custom page linked above.');

        $templateForm->addElement('header', 'textarea', 'Header (4)', array('rows'=>8));
        $templateForm->addElement('footer', 'textarea', 'Footer (5)', array('rows'=>8));
        $templateForm->addElement('widget_footer', 'text', 'Widget footer (6)');

        $templateForm->addElement('css', 'textarea', 'Custom CSS style', array('rows'=>8));
        $templateForm->addElement('head_section', 'textarea', '<meta> tags to be put in the <head> section', array('rows'=>8));

        $templateForm->setDefaultValues($template);

        if ($templateForm->isSubmitted() && $templateForm->validate($_POST)) {
            $data = array(
                'footer' => $_POST['footer'],
                'brand_name' => $_POST['brand_name'],
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
                'widget_footer' => $_POST['widget_footer'],
                'css' => $_POST['css'],
                'custom_page_name' => $_POST['custom_page_name'],
                'custom_page' => $_POST['custom_page'],
                'customize_favicon' => $_POST['customize_favicon'],
                'head_section' => $_POST['head_section'],
            );
            $this->templateModel->updateTemplate($this->user['id'], $data);
            
            if (isset($_POST['remove_brand_icon'])) {
                $this->attachmentModel->deleteFile($this->user['id'], 'brand_icon');
                $data['current_brand_icon'] = null;
            }
            if (!empty($_FILES['brand_icon']['name'])) {
                $this->attachmentModel->saveUploadedFile($this->user['id'], 'brand_icon', $_FILES['brand_icon']);
                $data['current_brand_icon'] = 'brand_icon';
            }
            $this->alert(__('Updated template', 'success'));
            header('Location: '.l('template', 'edit'));
            exit;
        }

        $this->render(array(
            'view' => 'admin/views/template/edit.php'
        ), array(
            'templateForm' => $templateForm
        ));
    }
}

?>