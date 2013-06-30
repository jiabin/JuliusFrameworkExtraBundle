# Date time picker

## Configuring bower

First you will need to configure SpBowerBundle to install JuliusFrameworkExtra's dependencies:

```
# app/config/config.yml
sp_bower:
    install_on_warmup: false # Optional
    keep_bowerrc: false # Optional
    bin: %bower_binary% # Optional
    bundles:
        JuliusFrameworkExtraBundle: ~
```

Once configured you will also need to install dependencies by executing `sp:bower:install` command. (See more info on SpBower: https://github.com/Spea/SpBowerBundle)

## Adding javascript & stylesheets

In your base template add following assets:

```
asset('julius_frameworkextra/components/bootstrap-datetimepicker/css/datetimepicker.css')
asset('julius_frameworkextra/components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')
```

## Using form type

To use date time picker simply change your form type to `julius_datetimepicker`

```
# Acme\DemoBundle\Form\Type\ExampleType:buildForm
$builder->add('field_name', 'julius_datetimepicker', $formOptions);
```