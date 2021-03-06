## Mayesto CSL

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/51e4d7811c1a44f2b99a59482f7e8e61)](https://app.codacy.com/app/mayesto/csl?utm_source=github.com&utm_medium=referral&utm_content=mayesto/csl&utm_campaign=Badge_Grade_Dashboard)

This pack was designed for more accurate checking of files

### Install

`composer global require mayesto/csl`

`ln -s ~/.composer/vendor/bin/csl /usr/bin`

### Usage

#### Check command
`csl check ./src --yaml=./config.yml`

##### RunOptions: 

    --format=value      Output format. Possible values: table, json
    --yaml=filepath     Select config file in yaml format
    -s|--short          Short file path on result
    -vvv                Debug mode

### Features

- Full configurable stock of rules
- Full configurable source of files. Options: Scanning dir, Git modified files

### Rules

- ClassAuthorPattern - Check class author with your regex
- ClassAuthorRequire - Require minimum one class author
- ClassMethodPhpDoc - Check if method has php doc
- ClassMethodPhpDocEmptyLineBeforeReturn - Check if method's php doc has invalid empty line
- ClassPhpDoc - Check if class has php doc
- ClassPhpDocPropertyRequire - Check if class's php doc has a property
- InternalFunctionNamespace - Find all call of native functions and check if they have fully namespace
- MethodReturnTypeRequire - Find all methods which have not return type cast
- ParserValidation - Basic parser validation
- TooMuchEmptyLines - Scan file and report too much empty lines
- EmptyLineOnEndOfFile - Check if file has empty line on end of file


### Config file

`At this moment file config is supported only in yaml format!`

Example of file:

```yaml
fileIterator: Mayesto\CSL\FileIterator\Git # Name of iterator class
rules: # Array of rules
  Mayesto\CSL\Rule\ParserValidation:

  Mayesto\CSL\Rule\ClassAuthorPattern:
    arguments:
      - '.*?<name@example.com>'

  Mayesto\CSL\Rule\ParserValidation:

  Mayesto\CSL\Rule\InternalFunctionNamespace:

  Mayesto\CSL\Rule\ClassPhpDoc:

  Mayesto\CSL\Rule\MethodReturnTypeRequire:

  Mayesto\CSL\Rule\ClassAuthorRequire:

  Mayesto\CSL\Rule\TooMuchEmptyLines:
    arguments:
      - 2 # Number of empty lines generating an error. Default: 2
      
  Mayesto\CSL\Rule\ClassMethodPhpDocEmptyLineBeforeReturn:

  Mayesto\CSL\Rule\EmptyLineOnEndOfFile:
  
  TestRule:
    file: /home/user/TestRule.php # Class which implements RuleInterface

```

### License

* [MIT](LICENSE)

### Author

This pack was made by Mayesto. If you have any question, send me an email. m@mayesto.pl