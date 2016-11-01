# simple-pdf

Generate PDF directly from PHP (in native PHP)

[![Build Status](https://travis-ci.org/PHPfriends/simple-pdf.svg?branch=master)](https://travis-ci.org/PHPfriends/simple-pdf)

Run the sample code

```
php src/Example/Example1.php
```

Should create a double page PDF document in the same Example folder that has to be visible with any PDF viewer.

## Compatibility notes

### FontDescriptor

- Beginning with PDF 1.5, the special treatment given to the standard 14 fonts is deprecated. All fonts used in a PDF document should be represented us- ing a complete font descriptor. For backwards capability, viewer applications must still provide the special treatment identified for the standard 14 fonts.
