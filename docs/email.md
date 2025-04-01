# Email logic and configuration

## AWS SES emails

When you want to use email on this project, you need to set the ``MAILER_DSN`` var.

An example is described on .env file :
````
###> symfony/mailer|symfony/amazon-mailer ###
#MAILER_DSN=null://null
MAILER_DSN=ses+smtp://USERNAME:PASSWORD@default?region=eu-west-3
###< symfony/mailer|symfony/amazon-mailer ###
````

These credentials are specific for each instance of this project. Ask the network team to have new identifier.

Be careful if the username or password contain special character you will need to encode them.

For example :

- `/` must be changed into `%2F`
- `+` must be changed into `%2B`

## References

* [Documentation officielle](https://symfony.com/doc/current/mailer.html)

## Credits

Created by [Numeric Wave](https://numeric-wave.eu)
