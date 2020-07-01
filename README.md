# YAML SEEDER

[![Maintainability](https://api.codeclimate.com/v1/badges/a6068ea69ed31f0e6827/maintainability)](https://codeclimate.com/github/AMBERSIVE/yamlseeder/maintainability)

!!! This package is not ready yet !!!

## Installation

```bash
composer require ambersive/yamlseeder
```

#### Optional: Publish the config

```bash
php artisan vendor:publish --tag=yaml-seeder
```

## Usage

All the seeds will be executed if you run the classic

```bash
php artisan db:seed
```

## Create yaml seed file

Create a yml file in *database/seeds-yaml*.
Please use the *.yml* file extension.

The structure of the the seed looks like:

```yml
model: \AMBERSIVE\YamlSeeder\Tests\Examples\Models\Migration
primaryKey: id
fieldsOnCreate:
  - migration
  - batch
data:
  - id: 99
    batch: 1
    migration: 'users_test_table'
```

**model**:
The model will define which data should be seeded.

**primaryKey**:
Default it is 'id' if not defined.

**exlude**:
Default is false.
This defines if the file will be included within the normal db:seed process.

**fieldsOnCreate**:
List of field name which defines which files will be need on a create statement. Make your fields nullable if you want to reduce the list.

**data**:
Array of the actual data.
Follow the structure of you model.

### Command

```bash
 php artisan make:seeder-yaml demo --model=App/User
```

This command will create in the *database/seeds-yaml* folder a demo.yml file.

## Feedback

Please feel free to give us feedback or any improvement suggestions.

## Security Vulnerabilities

If you discover a security vulnerability within this package, please send an e-mail to Manuel Pirker-Ihl via [manuel.pirker-ihl@ambersive.com](mailto:manuel.pirker-ihl@ambersive.com). All security vulnerabilities will be promptly addressed.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
