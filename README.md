# rental-restful-api

Custom API and Auth Service for Rental Software built from Felix Hildebrandt as final thesis for Software Engineering in 2020.
It features a SQL and Postman architecture for test and build purposes.

> **_NOTE:_** API Wiki and filenames appear in German.

## Description

The API was the backend for a car rental service that ran within the browser (HTML+PHP) and Mobile (iOS) which can be seen in the screenshots. The API was operated as a client-server model that was connected to a external database.

## Features

- Renting
- Image uploads
- Create and maintain customer bases
- Create and maintain car fleets
- Company data maintenance
- Employee management
- Authentication token

## API Wiki

This API features a fully fledged out documentation for all entities, functions, uploads, and authentification tokens in German. The wiki can be found in the `/wiki` folder of this repository which features more then 8 chapters of detailed code- and call description.

[Link to Documentation](/wiki/)

## Testing

Within this repository, API tests to all calls are included in the `/testful` folder. Therefore, a sample MySQL database is provided which can be set up on a local server, for instance a XAMPP configuration. All tests are already grouped together based on their entity and fully appear in JSON, so they can be imported into Postman directly.

[Link to Testscripts](/testful/)

## Screenshots

> Frontends are only used for illustrative purposes and not included in the repository, as they were built by students utilizing this API for their own software products. However, the frontends show what functionality the API service is capable of.

![Screenshot 1](./img/screenshot_01.png)
![Screenshot 2](./img/screenshot_02.png)
![Screenshot 3](./img/screenshot_03.png)
![Screenshot 4](./img/screenshot_04.png)

## Tools

- [MySQL](https://www.mysql.com/)
- [Postman](https://www.postman.com/)
- [XAMPP](https://www.apachefriends.org/de/index.html)
