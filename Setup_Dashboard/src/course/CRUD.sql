CREATE TABLE employees (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50),
    salary NUMERIC(10, 2),
    date_hired DATE DEFAULT CURRENT_DATE
);


INSERT INTO employees (name, position, salary) 
VALUES 
('Ram', 'Manager', 75000),
('Dev', 'Developer', 55000),
('Surya', 'Developer', 55000);

/*Get all Records*/
SELECT * FROM employees;

/*Get specific records:*/


SELECT name, salary FROM employees WHERE position = 'Developer';

/* Update (Modify Data)
Update existing records:*/

UPDATE employees 
SET salary = 60000 
WHERE name = 'Jane Smith';

/*Delete records from the table:*/

DELETE FROM employees 
WHERE id = 1;



for spatial table

CREATE TABLE locations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    latitude NUMERIC(10, 7) NOT NULL,
    longitude NUMERIC(10, 7) NOT NULL
);


INSERT INTO locations (name, latitude, longitude) 
VALUES
('Shangbangla Village,Ribhoi', 25.95, 91.86),
('Kaspi Vilage, Near Nag Mandir,West Kameng', 27.20, 92.56);



CREATE TABLE locations1 (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    geom GEOMETRY(Point, 4326) -- WGS 84 spatial reference system
);


INSERT INTO locations1 (name, geom)
VALUES 
('Shangbangla Village,Ribhoi', ST_SetSRID(ST_Point(25.95, 91.86), 4326)), -- (Longitude, Latitude)
('Kaspi Vilage, Near Nag Mandir,West Kameng', ST_SetSRID(ST_Point(27.20, 92.56), 4326));

