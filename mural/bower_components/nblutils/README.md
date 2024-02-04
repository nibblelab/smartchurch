# nblutils

JS and angularjs modules with many useful functions like factories for days and provinces

## Getting Started

Simply include the lib into your project (with momentjs as well) and add the dependency in your angular module 

```
bower install nblutils
```

```
<script src="bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="bower_components/nblutils/dist/js/nblutils.min.js"></script>
```

```javascript
angular.module('your_module', ['nblutils']);
```

### Using


#### Get provinces array

```javascript
var provinces = $provinces.get('br');
```

#### Get months array

```javascript
var months = $months.get('br');
```

#### Get days array

```javascript
var days = $days.get('br');
```

#### Wrapper for localstorage object

```javascript
$localstorage.set('key', value);
var v = $localstorage.get('key' [, defalt_value]);


$localstorage.setObject('key', valueObject);
var obj = $localstorage.getObject('key' [, default_object_value]);
```

#### Loading Spinner

In your HTML add this to the bottom

```javascript
<div id="loadingdiv">
    <img src="path_to_your_ajax_loading_image" class="ajax-loader"/>
</div>
```

Include CSS

```javascript
<link href="bower_components/nblutils/dist/css/nblutils.min.css" rel="stylesheet" type="text/css" media="screen, print, projection">
```

Add config to your angular module

```javascript
    .config(['$httpProvider', function($httpProvider) {
            $httpProvider.defaults.headers.common = {};
            $httpProvider.defaults.headers.post = {};
            $httpProvider.defaults.headers.get = {};
            $httpProvider.defaults.headers.put = {};
            $httpProvider.defaults.headers.patch = {};
            $httpProvider.interceptors.push('loadingInterceptor');
            var spinnerFunction = function (data, headersGetter) {
                angular.element('#loadingdiv').show();
                return data;
            };
            $httpProvider.defaults.transformRequest.push(spinnerFunction);
        }
    ])
```

This will execute the loading spinner on every HTTP request

#### Format Money

Use this extension method to convert float into money formatted strings:

```javascript
var money_str = money_float.formatMoney(number_of_decimals [, decimal_point [, thousand_separator]]);
```

#### Replace Last

Use this extension method to replace the last occurence of a substring in a string

```javascript
var original_str = "This is only the beginning";
var new_str = original_str.replaceLast('th'); // This is only e beginning
```

#### Replace All

Use this extension method to replace all occurences of a substring in a string

```javascript
var original_str = "This is only the beginning";
var new_str = original_str.replaceAll('th'); // is is only e beginning
```

#### Format CNJP

```javascript
var cnpj = "00111222000101";
var formatted = cnpj.formatCNPJ(); // 00.111.222/0001-01
```

#### Format Time

```javascript
var time = "133001";
var formatted = time.formatTime(); // 13:30:01
```

#### Format CEP

```javascript
var cep = "0100570";
var formatted = cep.formatCEP(); // 01005-70
```

#### Format Phone

```javascript
var phone = "12345678";
var formatted = phone.formatPhone(); // 1234-5678

var phone = "3412345678";
var formatted = phone.formatPhone(); // (34) 1234-5678

var phone = "34912345678";
var formatted = phone.formatPhone(); // (34) 9 1234-5678

var phone = "0800401402";
var formatted = phone.formatPhone(); // 0800 401-402

var phone = "08004014130";
var formatted = phone.formatPhone(); // 0800 401-4130
```

#### Clean Money String

```javascript
var money_str = "1.250,75";
var money_clean = money_str.cleanMoney(); // 1250.75 (string)
```

#### Convert Money string to float

```javascript
var money_str = "1.250,75";
var money_clean = money_str.cleanMoney(); // 1250.75 (float)
```

#### Check if string only contains numbers 

```javascript
var a = "1.250,75";
var b = "1250.75";
if(a.isNumeric()) {  } // false
if(b.isNumeric()) {  } // true
```

#### Check if string is valid CPF

```javascript
var a = "123.456.789-00";
var b = "825.858.524-07";
if(a.validCPF()) {  } // false
if(b.validCPF()) {  } // true
```

#### Check if string is valid CNPJ

```javascript
var a = "12.234.456/7890-01";
var b = "06.848.185/0001-75";
if(a.validCNPJ()) {  } // false
if(b.validCNPJ()) {  } // true
```

#### Generate random sequence of digits

```javascript
getRandomSequence(5); // 80325
getRandomSequence(7); // 9982063
```

#### Generate random ID

```javascript
getRandomId(); // 201709070705318032520630
```

#### Generate a blank image (base64)

```javascript
var img = UtilImage.whiteOne;
```

#### Email validation

To validate email strings use

```javascript
if(email_string_var.validEmail()) {
    //it's valid
}
```

#### Accented Letters

To convert accented letters into not accented ones

```javascript
var accented = "Isto é um teste de pão";
var not_accented = accented.removeAccentedLetters(); // returns Isto e um teste de pao
```

#### Capitalize First Letter

To capitalize the first letter in string

```javascript
var str = "not capitalized";
var cap_str = str.capitalizeFirstLetter(); // returns Not capitalized
```

#### Array shuffle

To shuffle an array 

```javascript
var arr = [1,2,3,4,5];
shuffleArray(arr); // shuffle arr
```

#### Search address by CEP (Brazil)

```javascript
var cep_value = '38.415-054'; // value with ou without . and -
var use_https = true; // optional flag to use HTTPS. Default: true
$cep.getAddress(cep_value, use_https).then(function(r) { 
    console.log(r);
    /*
    result is an object:
    {
        "cep": "38415-054",
        "logradouro": "Rua Diabase",
        "complemento": "",
        "bairro": "Dona Zulmira",
        "localidade": "Uberlândia",
        "uf": "MG",
        "unidade": "",
        "ibge": "3170206",
        "gia": ""
    }
    */
}, function(e) { console.log(e); });
```

#### Check if URL contain image 

```javascript
if(isURLAnImage(_var))
{
    // _var contains an image URL
}
```

#### Test var

```javascript
if (VarTester.isVoidStr(_var)) {
    // _var is a string
}
if (VarTester.isEmpty(_var)) {
    // _var is a empty string
}
if (VarTester.isNull(_var)) {
    // _var is null
}
if (VarTester.isUndefined(_var)) {
    // _var is undefined
}
if (VarTester.isZeroInt(_var)) {
    // _var is integer and 0
}
if (VarTester.isZeroFloat(_var)) {
    // _var is float and 0
}
if (VarTester.isEmptyObject(_var)) {
    // _var is an empty object
}
if (VarTester.isVoidStr(_var)) {
    // _var is an invalid string
}
```

### i18n

To add provinces, days or months simply extend nblutilsdata object

```
nblutilsdata.days['us'] = [
    { code: "Sun", num: '0', name: "Sunday" },
    { code: "Mon", num: '1', name: "Monday" },
    { code: "Tue", num: '2', name: "Tuesday" },
    { code: "Wed", num: '3', name: "Wednesday" },
    { code: "Thu", num: '4', name: "Thursday" },
    { code: "Fri", num: '5', name: "Friday" },
    { code: "Sat", num: '6', name: "Saturday" }
]
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details


## Inspiration 

* formatMoney from krosti: https://gist.github.com/krosti/4026177

