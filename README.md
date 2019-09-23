# Lyfter wp-cli toobox
This wp-cli package is a toolbox containing useful commands for Wordpress development

## Installing
Naturally this package requires wp-cpi. If you do not have wp-cli installed on your machine, visit https://wp-cli.org/#installing to install.

After installing wp-cli you're ready to install this package using the package command:

```bash
 wp package install https://github.com/peters97/wp-cli-toobox.git
```

## Usage
```bash
NAME

  wp lyfter

DESCRIPTION

  Toolbox containing useful commands for Wordpress development.

SYNOPSIS

  wp lyfter <command>

SUBCOMMANDS

  replace         Global search and replace for the wp database.

```


### `replace`
Searches all the posts, postmeta and options for the given value and replaces it. Keeping any serialised arrays in mind.

```
wp lyfter replace <find> <replace>
```