### News

##### Certificate change

*2024-03-21*

aqi.eco uses Let's Encrypt service to provide secure access via https protocol. This protocol is also used to receive data from the sensors. In the last month, [Let's Encrypt updated its core certificate](https://letsencrypt.org/2023/07/10/cross-sign-expiration) from *DST Root CA X3* to *ISRG Root X1*.

sensor.community devices and Nettigo Air Monitor devices have the old *DST Root CA X3* certificates hardcoded in their firmware. As a result, they'll be no longer able to send their data to aqi.eco after this change.

**NAM**

Nettigo provided new firmware: [NAMF-2020-45a](https://github.com/nettigo/namf/releases/tag/NAMF-2020-45a) (for stable users) or [NAMF-2020-46rc4](https://github.com/nettigo/namf/releases/tag/NAMF-2020-46rc4) (for beta users). These versions will be able to push data to aqi.eco even after the certificate change.

In most cases the firmware will be updated automatically. If it's not, please open the NAM settings page and click:

1. *Configuration*
2. *Advanced*
3. Make sure the *Auto update firmware, using channel* is checked.
4. As a chanel, choose *stable* or *beta*.

**sensor.community**

sensor.community currently doesn't provide firmware accepting the *ISRG Root X1* certificate. Respective issue can be found here: https://github.com/opendata-stuttgart/sensors-software/pull/1015

The upcoming firmware version which will include this change is `NRZ-2020-133-P1`.

As a workaround, the https protocol can be disabled completely in the device settings. In order to do that:

1. Open device configuration page.
2. Find option *Send data to own API* (should be checked).
3. Find *Port* a few fields below. If you use https, it'll be `443`. Replace it with `80`.
4. Click *Save and restart*.
