# Expandable Online Portfolio
A simple expandable portfolio management system built for other developers. It features custom made model-view-controller architecture framework that can help ease on expanding the application.

## Portfolio System
Customize your own portfolio website, display your works, provide your CV/Resume for download, and get your possible clients contact you.

## Custom Framework
The framework follows model-view-controller (MVC) architecture pattern.

### Setup
1. Clone this repository.
2. Run `composer install` to install project dependencies.
3. Create `.env` file from `.env.example` and fill up with correct values.
4. Import the SQL file on `sql_importables` folder to web server's MySQL.
5. Upload the project to public root folder of server.
6. Change the admin password by logging in at `yourserver.com/admin/login` with `admin0` as username and password and navigating to `yourserver.com/admin/settings`.

### Routing
The application uses dynamic routing for selection of controller and action.
> yourserver.com/controller/action/parameter1/parameter2/...

The application parses the URL to get the controller and action as well as the parameters. The default value for controller and action is `Index` which means that the home page would be handled by `IndexController` and its `Index` function. The action can also receive a request which is injected by the router. The request parameter holds an associative array which contains `url, form, files` keys.

### Controllers
Creation of controller is simple. Follow the format of existing controllers on `/app/Controllers` folder.

### Model
Models are powered by a simple ORM. Creation of model is done by creating a file named after singular form of the table in database. However, the `$table` property can still be used to override this rule. The model should extend the `Model` class.

**Usage (Example using User model)**

Upon creation of model, the property `$fillables` should be filled up with columns of tables that can be filled up with creation or update.

Getting entries are done by calling the static function `find`.

> `User::find(1) // Finds all entries.`

> `User::find('id', 3) // Finds all entries where id is 3.`

> `User::find('username', '=', 'admin') // Finds all entries where username is equals to admin.`

Creation of entries is done by calling the static function `create`.

> `User::create([['username' => 'A'], ['username' => 'B']]) // Creates two user entries.`

### Views
Views are powered by Symfony's Twig templating system. Rendering a view is done by running the following snippet on your controller below:

> `self::render('view-name', ['name' => 'Sample']) // The Twig file on views folder will automatically be rendered.`

**SASS/SCSS**

CSS Styling can be done with SASS/SCSS. The files can be found on `/resources/scss` folder. Running the following command will automatically process all changes in your SCSS file during production to `/public/css` folder.

> `sass --watch resources/scss:public/css`

### Other Features
There are also other features such as `Database`, `Authentication` and `Upload` which helps on managing other operations.

**Database**

The database class follows singleton pattern. The application only allows one instance of database connection all throughout the application. Getting the connection can be done by:

> `Database::getInstance()->getConnection()`

**Authentication**

This is used for checking the login status of user and managing other things regarding authentication.

**Upload**

This handles uploading of files into system.

### Contact/Help

For questions and inquiries, please contact me at edceliz01@gmail.com.