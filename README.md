WordPress theme based on [Twig](https://github.com/twigphp/Twig) and [Timber plugin](https://github.com/timber/timber)

## Instal required package managers

https://nodejs.org/en/

https://yarnpkg.com/lang/en/docs/install

## Install frontend dependecies

From the theme directory run: `yarn install`

## Build assets

* Production `yarn build-assets`
* Development `yarn start`

## Deployment using [ftp-deployment](https://github.com/dg/ftp-deployment)

* Find and rename `deployment.sample.ini` to `deployment.ini`, then edit the file and fill your ftp credentials.
* Download `deployment.phar` to theme directory from [ftp-deployment](https://github.com/dg/ftp-deployment)
* Run `php deployment.phar deployment.ini`.
