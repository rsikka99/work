# Integrating the PHPOffice/PHPWord Library


##General Changes that Apply to Files Referencing PHPWord

```PHPWord``` is changed to ```\PhpOffice\PhpWord\PhpWord```

```PHPWord_Section``` is now ```\PhpOffice\PhpWord\Element\Section```

```PHPWord_IOFactory::createWriter($this->phpword, 'Word2007')``` is now  ```\PhpOffice\PhpWord\IOFactory::createWriter($this->phpword, 'Word2007')```

```PHPWord_Style_ListItem::``` is now ```\PhpOffice\PhpWord\Style\ListItem::```

##Method Changes:

```$properties = $phpWord->getProperties()``` is deprecated - use ```$properties = $phpWord->getDocumentProperties()```

```$properties = $phpWord->getProperties()``` now needs to be ```$properties = $phpWord->getDocumentProperties()```

```$phpWord->createSection()``` is deprecated - use ```$phpWord->addSection()```

##Necessary Fixes:

For setting background color (of title page, subheadings, etc..)

```php
'shading'    => array(
    'fill' => $titlePageBackgroundColor1,
)
```
Note: For ```addTitleStyle()```, the ```'shading'``` property has to be declared in the second array in title style (ie. the paragraph style array)

###To remember:

* Don't use empty rows (document breaks, won't render)
* Can use ```array_merge($this->style,$this->style2)``` to combine properties if necessary

