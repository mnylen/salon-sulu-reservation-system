-- Create a few employees
INSERT INTO employees (first_name, last_name, active) VALUES ('Jack', 'Daniels', true);		-- ID = 1
INSERT INTO employees (first_name, last_name, active) VALUES ('Camille', 'Smith', true);	-- ID = 2
INSERT INTO employees (first_name, last_name, active) VALUES ('Dick', 'Johnson', true);		-- ID = 3
INSERT INTO employees (first_name, last_name, active) VALUES ('Jennifer', 'Hall', true);		-- ID = 4
INSERT INTO employees (first_name, last_name, active) VALUES ('Pamela', 'Andersson', true);		-- ID = 5

-- Generate some work shifts
select gen_shifts();

-- Add some services
INSERT INTO service_catalog (name, duration, price, description) VALUES ('Buzzcut', 15, 13.00, 'A buzzcut.');
INSERT INTO service_catalog (name, duration, price, description) VALUES ('Haircut', 30, 25.00, 'Traditional haircut as you wish.');
INSERT INTO service_catalog (name, duration, price, description) VALUES ('Crew cut', 45, 20.00, 'Always modern crew cut - now in special price, just for you!');
INSERT INTO service_catalog (name, duration, price, description) VALUES ('Single dye + haircut', 120, 40.00, 'Haircut and dye with single color.');
INSERT INTO service_catalog (name, duration, price, description) VALUES ('Double dye + haircut', 120, 55.00, 'Haircut and dye with two colors.');

-- Add performing employees

-- Konesiili
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (1, 1);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (2, 1);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (3, 1);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (4, 1);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (5, 1);

-- Hiustenleikkuu
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (1, 2);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (2, 2);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (3, 2);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (4, 2);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (5, 2);

-- Jenkkisiili
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (3, 3);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (5, 3);

-- Väripaketti
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (1, 4);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (2, 4);
INSERT INTO employee_service_catalog (employee_id, service_id) VALUES (4, 4);