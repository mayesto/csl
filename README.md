## Mayesto CSL

This pack was designed for more accurate checking of files

### Install

`composer global require mayesto/csl`
`ln -s ~/.composer/vendor/bin/csl /usr/bin`

### Usage

#### Check command
`./bin/csl csl:check ./src --yaml=./config.yml`

##### RunOptions: 

    --format=value      Output format. Possible values: table, json
    --yaml=filepath     Select config file in yaml format
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

  Mayesto\CSL\Rule\ClassMethodPhpDocEmptyLineBeforeReturn:

  TestRule:
    file: /home/user/TestRule.php # Class which implements RuleInterface

```

### License

* [MIT](LICENSE)

### Author

This pack was made by Mayesto. If you have any question, send me an email. m@mayesto.pl