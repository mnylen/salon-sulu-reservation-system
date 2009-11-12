/* ============================================================================
 * Drop any previous tables, views and sequences
 * ============================================================================
 */

-- employees table
DROP SEQUENCE IF EXISTS employees_id_seq CASCADE;
DROP TABLE IF EXISTS employees CASCADE;

CREATE SEQUENCE employees_id_seq;
CREATE TABLE employees
(
	id			BIGINT			NOT NULL DEFAULT nextval('employees_id_seq'),
	first_name	VARCHAR(255)	NOT NULL,
	last_name	VARCHAR(255)	NOT NULL,
	active      BOOLEAN         NOT NULL DEFAULT TRUE,
	
	PRIMARY KEY (id)
);

-- work_shifts table
DROP SEQUENCE IF EXISTS work_shifts_id_seq CASCADE;
DROP TABLE IF EXISTS work_shifts CASCADE;

CREATE SEQUENCE work_shifts_id_seq;
CREATE TABLE work_shifts
(
	id			BIGINT		NOT NULL DEFAULT nextval('work_shifts_id_seq'),
	employee_id	BIGINT		NOT NULL,
	start_time	TIMESTAMP	NOT NULL,
	end_time	TIMESTAMP	NOT NULL,
	
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees
);

-- service_catalog table
DROP SEQUENCE IF EXISTS service_catalog_id_seq CASCADE;
DROP TABLE IF EXISTS service_catalog CASCADE;
			
CREATE SEQUENCE service_catalog_id_seq;
CREATE TABLE service_catalog
(			
	id			BIGINT		NOT NULL DEFAULT nextval('service_catalog_id_seq'),
	name		VARCHAR(50)	NOT NULL,
	description	TEXT,
	duration	SMALLINT	NOT NULL,
	price		DOUBLE PRECISION NOT NULL,
	available	BOOLEAN		DEFAULT TRUE,
	
	PRIMARY KEY (id)
);

-- employee_service_catalog table
DROP SEQUENCE IF EXISTS employee_service_catalog_id_seq CASCADE;
DROP TABLE IF EXISTS employee_service_catalog CASCADE;

CREATE SEQUENCE employee_service_catalog_id_seq;
CREATE TABLE employee_service_catalog
(
	id			BIGINT		NOT NULL DEFAULT nextval('employee_service_catalog_id_seq'),
	service_id	BIGINT		NOT NULL,
	employee_id	BIGINT		NOT NULL,
	
	PRIMARY KEY (id),
	FOREIGN KEY (service_id) REFERENCES service_catalog,
	FOREIGN KEY (employee_id) REFERENCES employees
);

-- reservable_services view
DROP VIEW IF EXISTS reservable_services;
CREATE VIEW reservable_services AS
	SELECT * FROM service_catalog sc
		WHERE sc.available = TRUE AND
			(SELECT COUNT(id) FROM employee_service_catalog esc
				WHERE esc.service_id = sc.id) > 0;

-- reservations table
DROP SEQUENCE IF EXISTS reservations_id_seq CASCADE;
DROP TABLE IF EXISTS reservations CASCADE;

CREATE SEQUENCE reservations_id_seq;
CREATE TABLE reservations
(
	id			BIGINT		NOT NULL DEFAULT nextval('reservations_id_seq'),
	employee_id	BIGINT		NOT NULL,
	service_id	BIGINT		NOT NULL,
	start_time	TIMESTAMP	NOT NULL,
	end_time	TIMESTAMP	NOT NULL,
	price		DOUBLE PRECISION NOT NULL,
	cancel_key	CHAR(5)		NOT NULL,
	cancelled	BOOLEAN		NOT NULL DEFAULT FALSE,
	
	cust_fname	VARCHAR(50)	NOT NULL,
	cust_lname	VARCHAR(50)	NOT NULL,
	cust_email	VARCHAR(100)NOT NULL,
	cust_phone	VARCHAR(15) NOT NULL,
	
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees,
	FOREIGN KEY (service_id) REFERENCES service_catalog
);

-- managers table
DROP SEQUENCE IF EXISTS managers_id_seq CASCADE;
DROP TABLE IF EXISTS managers CASCADE;

CREATE SEQUENCE managers_id_seq;
CREATE TABLE managers
(
	id			BIGINT		NOT NULL DEFAULT nextval('managers_id_seq'),
	username	VARCHAR(50)	NOT NULL,
	password	CHAR(32)	NOT NULL,
	
	PRIMARY KEY (id)
);

-- sessions table
DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions
(
	id				CHAR(32)	NOT NULL,
	manager_id		BIGINT		NOT NULL,
	last_activity	TIMESTAMP	NOT NULL,
	
	PRIMARY KEY (id),
	FOREIGN KEY (manager_id) REFERENCES managers
);

-- gen_sess_id()
CREATE OR REPLACE FUNCTION gen_sess_id() RETURNS char(32) AS $$
DECLARE
    i smallint;
    generated_key char(32);
    temp char(32);
    count_rec RECORD;
BEGIN
    i := 0;
    generated_key := '';

    WHILE i < 32 LOOP
        temp := generated_key || chr( cast(32 + random()*93 as integer) );
        generated_key := temp;

        i := i+1;
    END LOOP;

    SELECT INTO count_rec id FROM sessions WHERE id = generated_key;

    IF FOUND THEN
        RETURN gen_sess_id();
    ELSE
        RETURN generated_key;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- gen_cancel_key()
CREATE OR REPLACE FUNCTION gen_cancel_key() RETURNS char(5) AS $$
DECLARE
	i smallint;
	generated_key char(5);
	temp char(5);
	count_rec RECORD;
BEGIN
	i := 0;
    generated_key := '';

    WHILE i < 5 LOOP
        temp := generated_key || cast(random()*9 as integer);
        generated_key := temp;

        i := i+1;
    END LOOP;

    SELECT INTO count_rec id FROM reservations WHERE
    	cancel_key = generated_key AND
    	cancelled = FALSE AND
    	start_time > NOW();
    	
    IF FOUND THEN
    	RETURN gen_cancel_key();
    ELSE
    	return generated_key;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- gen_shifts()
CREATE OR REPLACE FUNCTION gen_shifts() RETURNS integer AS $$
DECLARE
    emp_rec RECORD;
    curr_date date;
    end_date date;
    begin_hour interval;
    last_shift RECORD;
BEGIN
    curr_date := current_date;
    end_date  := cast(curr_date + '30 days'::interval as date);
    
    FOR emp_rec IN SELECT id FROM employees WHERE active = TRUE LOOP
    	SELECT INTO last_shift DATE(MAX(start_time)) AS max FROM work_shifts WHERE employee_id = emp_rec.id;
    	
    	IF FOUND THEN
    		IF last_shift.max IS NOT NULL THEN
    			curr_date := cast(last_shift.max + '1 day'::interval as date);
    		END IF;
    	END IF;
    	
        WHILE curr_date <= end_date LOOP
            IF extract(dow from curr_date) BETWEEN '1' AND '5' THEN
                begin_hour := ((cast( (random() * 4) as integer) + 8) || ' hours')::interval;
                
                INSERT INTO work_shifts (employee_id, start_time, end_time) VALUES (
                    emp_rec.id, ( curr_date + begin_hour ), ( curr_date + begin_hour + '8 hours'::interval ));
            END IF;

            curr_date := cast(curr_date + '1 day'::interval as date);
        END LOOP;

        curr_date := current_date;
    END LOOP;

    RETURN 0;
END;
$$ LANGUAGE plpgsql;