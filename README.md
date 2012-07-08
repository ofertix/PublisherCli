What is "PublisherCli"?
=======================

PublisherCli is the component that allows publish events or stats using a console command.

You only need to get one file: `publisher_cli.phar`.

This file contains configuration and commands to change it.


Requirements
============

- PHP 5.3.2 and up witch pcntl extension installed.
- RabbitMQ or ZMQ.


Libraries and services used
===========================

- PHP
	- Phar
	- Symfony Components:
		- ClassLoader
		- YAML
	- PhpAmqpLib
- RabbitMQ/ZMQ+OpenPGM


Installation
============

The best way to install is to clone the repository and then configure as you need. See "Configuration" section.

After cloning you must install dependencies using "composer":

	php composer.phar update


Usage
=====

Help:
	php publisher_cli.phar --help

Publish stat:
	php publisher_cli.phar --name=[stat_name] --types=[type1,type2...] --values=[STDIN|value,STDIN|value...]

Publish event:
	php publisher_cli.phar --name=[event_name] --event

Configure:
	get current configuration:
	    php publisher_cli.phar config

	set configuration:
	    php publisher_cli.phar config set [param1.subparam1.subsubparam1] [new_value]
	

Configuration
=============

All configuration is done using a YAML file.

You only have to configure the publisher class and options.

See config file for more details.


Extra notes
===========

Use of ZMQ is discontinued because a memory leak using ZMQ with OpenPGM PUB/SUB.
