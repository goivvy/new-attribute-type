# Magento 2 New Attribute Type Extension

This extension creates a new product attribute input type.
 
Installation instructions:

* Clone the repository

```console
git clone https://github.com/goivvy/new-attribute-type.git
```

* Copy `app` folder

```console
cp -r ~/new-attribute-type/app /path/to/magento2/root/folder
```

* Install and recompile

```console
php bin/magento setup:upgrade
php bin/magento deploy:mode:set production
```
