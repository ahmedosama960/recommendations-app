# Reading Recommendation System   [ You Can change to any type of recommendations you want ]

## Features

- **Submit Reading Intervals**: Users can record the start and end pages of their reading sessions for any book in the system.
- **Top Book Recommendations**: The system calculates and displays the top five books with the most unique pages read.

These instructions will  make you able to run the project 
### Prerequisites

- PHP >= 8.1
- Composer
- Laravel >= 10.0
- PostgreSQL

### Installation

 **Clone the repository**
1-Install dependencies
    composer install

2- setup the .env file

3- generate a key 
     php artisan key:generate

4- Migrate the data and using the seeder
    php artisan migrate
    php artisan db:seed

The project is ready to be served using the serve command 
php artisan serve

Visit this next link to use the documenation and api request 
    your_base_url/api/documentation
