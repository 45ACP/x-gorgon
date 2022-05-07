## Description

```js
 * TikTok's network traffic is protected using HTTPS
 * Sometimes an additional layer of encryption named "ttEncrypt" is used. 
 * TikTok’s API requests are protected with a custom signature in the HTTP header named "X-Gorgon"
 * This signature is to prevent 3d-party programs from making TikTok API requests.
```

## Notes

device-id-x-gorgon
The ins and outs of the iOS Jitterbit wind control encryption algorithm

The ins and outs of the iOS Jitterbit encryption algorithm (a)
The encryption algorithm of the Jitterbug communication protocol is the most perfect so far, some key functions have been confused by VM, such as device registration, video information, and other common interfaces, which can only be understood through dynamic debugging traces to understand the process.

Let's first analyze how the common device registration is generated, which is the first step in requesting a Jitterbug interface, without it, any interface requesting Jitterbug will not return data.

1. Jitterbit's device registration interface
https://log.snssdk.com/service/2/device_register/?
method: POST

body: device information encrypted data

URL parameter: device information parameters

2. Device information parameter generation
The generation of device_id is calculated based on the parameters we submit to Jitterbug, so we have to generate some random parameters.

Key parameters:
carrier display_name field: this field is not utf-8 encoding, it is GBK encoding, we have to do encoding conversion

Idfa, VendorID field: standard UUID algorithm to generate can

Openudid: randomly generated

Trace debugging process omitted ......

Through dynamic debugging finally located to sub_101E7830 device encryption function

The parameter of the sub-function is a dictionary passed in
```js
{
    fingerprint = "";
    header = {
        access = WIFI;
        aid = 1128;
        "app_language" = en;
        "app_name" = aweme;
        "app_region" = CN;
        "app_version" = "8.7.1";
        carrier = "\U4e2d\U56fd\U79fb\U52a8";
        channel = AppStore;
        custom = {
            "app_language" = zh;
            "app_region" = CN;
            "build_number" = 87100;
            "earphone_status" = on;
        };
        "device_id" = ;
        "device_model" = "iPhone X";
        "display_name" = "\U6296\U97f3\U77ed\U89c6\U9891";
        idfa = "E3D93D3-U747-R394-E2033-HF383J3984JE";
        "install_id" = ;
        "is_jailbroken" = 0;
        "is_upgrade_user" = 1;
        language = zh;
        mc = "00:00:00:00:00:00";
        "mcc_mnc" = "";
        openudid = 9234923948d9392934dkk3939935d93939r3a3s3;
        os = iOS;
        "os_version" = "12.1";
        package = "com.ss.iphone.ugc.Aweme";
        region = CN;
        resolution = "1024*768";
        "sdk_version" = 0011;
        timezone = 1;
        "tz_name" = "Asia/Shanghai";
        "tz_offset" = 99000;
        "user_agent" = "Aweme 8.7.1 rv:87100 (iPhone; iPhone OS 12.1; zh_CN) Cronet";
        "vendor_id" = "6J3DJD34-3DE4-R3KD-DS33-739394839384";
    };
    "magic_tag" = "ss_app_log";
}
```

All we have to replace is the vendor_id, openudid, and idfa in the JSON and then encrypt it

The algorithm of the encryption process is AES, first, use standard Gzip compression parameters and then call AES for encryption. android and iOS encryption method is the same.

The flowchart is as follows.
Flowchart

After completing the above steps you can submit the device registration request, the result of the successful request is as follows.
```js
{
	"server_time": 1557474647,
	"device_id": 61853858364,
	"install_id": 99375638378,
	"device_id_str": "61853858364",
	"install_id_str": "99375638378",
	"new_user": 1
}
```

iOS Jitterbug 8 series version for device registration of the latest wind control more than a log algorithm.

5. Other
In addition to the device_register algorithm, there are mas, as, cp, and X-gorgon algorithms.
