
-- STATE
-- Just a lookup table for state abbreviations, not linked to any tables
CREATE TABLE state (
    abbr                    CHAR(2) PRIMARY KEY,
    name                    VARCHAR(35)
);

-- LEVEL
-- Level of this position, such as federal, state, county, etc.
CREATE TABLE level (
    id                      CHAR(1) PRIMARY KEY,
    name                    VARCHAR(12)
);

-- OFFICE HOLDER
-- A table to hold information about who is occupying this office
CREATE TABLE office_holder (
    id                      SERIAL PRIMARY KEY,
    first_name              VARCHAR(25) NOT NULL,
    middle_name             VARCHAR(25),
    last_name               VARCHAR(25) NOT NULL,
    party_affiliation       CHAR(1) CHECK (party_affiliation IN ('I', 'D', 'R', '')),
    address1                VARCHAR(25),
    address2                VARCHAR(25),
    city                    VARCHAR(25),
    state                   CHAR(2),
    zip                     CHAR(5),
    phone                   VARCHAR(15),
    fax                     VARCHAR(15),
    email_address           VARCHAR(30),
    website                 VARCHAR(50),
    photo_link              TEXT,
    notes                   TEXT
);

-- election_div
-- A table to hold information about each voting elections divisioin including maps, name, scope, etc.
CREATE TABLE election_div (
    id                      SERIAL PRIMARY KEY,
    name                    VARCHAR(50) NOT NULL,
    phys_addr_addr1         VARCHAR(25),
    phys_addr_addr2         VARCHAR(25),
    phys_addr_city          VARCHAR(25),
    phys_addr_state         CHAR(2),
    phys_addr_zip           CHAR(5),
    mail_addr_addr1         VARCHAR(25),
    mail_addr_addr2         VARCHAR(25),
    mail_addr_city          VARCHAR(25),
    mail_addr_state         CHAR(2),
    mail_addr_zip           CHAR(5),
    phone                   VARCHAR(15),
    fax                     VARCHAR(15),
    website                 TEXT,
    notes                   TEXT
);

-- election_div_docs
-- A table to hold links to documents that all candidates in an election division must submit
CREATE TABLE election_div_docs (
    id                      SERIAL PRIMARY KEY,
    election_div_id         INTEGER REFERENCES election_div(id),
    name                    VARCHAR(35) NOT NULL,
    link                    TEXT NOT NULL
);

-- DISTRICT
-- Contains all the districts that a voter may live in such as city, county, state, country, etc
CREATE TABLE district (
    id                      SERIAL PRIMARY KEY,
    state                   CHAR(2),
    name                    VARCHAR(50),
    level_id                CHAR(1) REFERENCES level(id),
    election_div_id         INTEGER REFERENCES election_div(id)
);

CREATE TABLE office (
    id                      SERIAL PRIMARY KEY,
    title                   VARCHAR(35) NOT NULL,
    num_positions           INTEGER,
    responsibilities        TEXT,
    term_length_months      INTEGER,
    filing_fee              TEXT,
    partisan                BOOLEAN,
    age_requirements        INTEGER,
    res_requirements        TEXT,
    prof_requirements       TEXT,
    salary                  MONEY,
    notes                   TEXT
);

-- OFFICE POSITION
-- A table to hold office details
CREATE TABLE office_position (
    id                      SERIAL PRIMARY KEY,
    district_id             INTEGER REFERENCES district(id),
    office_id               INTEGER REFERENCES office(id),
    office_holder_id        INTEGER REFERENCES office_holder(id),
    position_name           VARCHAR(25),
    term_start              DATE,
    term_end                DATE,
    filing_deadline         DATE,
    next_election           DATE,
    notes                   TEXT
);

-- office_docs
-- A table to hold links to documents that are UNIQUE to an office
CREATE TABLE office_docs (
    id                      SERIAL PRIMARY KEY,
    office_id               INTEGER REFERENCES office(id),
    name                    VARCHAR(35) NOT NULL,
    link                    TEXT NOT NULL
);

CREATE TABLE congressional_district (
    num                     INTEGER,
    state                   CHAR(2),
    district_id             INTEGER REFERENCES district(id)
);
SELECT AddGeometryColumn( 'congressional_district', 'geom', 4326, 'MULTIPOLYGON', 2 );


-- BOUNDARY_MAP
-- A table to store boundary map data for the elect_divz
CREATE TABLE district_map (
    id                      SERIAL PRIMARY KEY,
    district_id             INTEGER REFERENCES district(id)
);
SELECT AddGeometryColumn( 'district_map', 'geom', 4326, 'MULTIPOLYGON', 2 );


CREATE TABLE county_map (
    name                    VARCHAR(35),
    state_abbr              CHAR(2) REFERENCES state(abbr)
);

ALTER TABLE county_map
ADD CONSTRAINT pk_county PRIMARY KEY (name, state_abbr);
SELECT AddGeometryColumn( 'county', 'geom', 4326, 'MULTIPOLYGON', 2 );
    
