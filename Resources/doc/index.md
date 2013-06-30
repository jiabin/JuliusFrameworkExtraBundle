# Documentation

## Requirements

This bundle currently depends on:

* [SP Bower Bundle](https://github.com/Spea/SpBowerBundle)

## Installation

### Step 1: Download JuliusFrameworkExtraBundle using composer

Add JuliusFrameworkExtraBundle in your composer.json:
```
{
    "require": {
        "jiabin/julius-framework-extra-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:
```
$ php composer.phar update jiabin/julius-framework-extra-bundle
```
Composer will install the bundle to your project's vendor/jiabin directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:
```
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Julius\FrameworkExtraBundle\JuliusFrameworkExtraBundle(),
    );
}
```

### Step 3: Configure the JuliusFrameworkExtraBundle

No configuration available yet

## Date time picker

To use date time picker simply change your form type to `julius_datetimepicker`

```
# Acme\DemoBundle\Form\Type\ExampleType:buildForm
$builder->add('field_name', 'julius_datetimepicker', $formOptions);
```