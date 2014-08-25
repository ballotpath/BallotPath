-- Create a district for State-wide Elections for State Positions
INSERT INTO district (state, name, level_id, election_div_id)
VALUES ('OR', 'State of Oregon for State Positions', 'S', (SELECT id FROM election_div WHERE name = 'State of Oregon') );
-- Create a district for State-wide Elections for Federal Positions
INSERT INTO district (state, name, level_id, election_div_id)
VALUES ('OR', 'State of Oregon for Federal Positions', 'F', (SELECT id FROM election_div WHERE name = 'State of Oregon') );

-- Create office holders for the Federal offices
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Barak', 'Hussein', 'Obama', 'D', 'http://www.blogcdn.com/www.engadget.com/media/2009/01/obamasportrait.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Ron','','Wyden','D','"http://upload.wikimedia.org/wikipedia/commons/e/e3/Ron_Wyden_official_portrait_crop.jpg"');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Jeff','','Merkley','D','http://upload.wikimedia.org/wikipedia/commons/7/74/Jeff_Merkley.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Suzanne','','Bonamici','D','http://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Suzanne_Bonamici.jpg/220px-Suzanne_Bonamici.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Greg','','Walden','R','https://beta.congress.gov/img/member/112_walden_or02.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Earl','','Blumenauer','D','http://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Earlblumenauer.jpeg/220px-Earlblumenauer.jpeg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Peter','','Defazio','D','http://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Peter_DeFazio,_Official_Portrait,_112th_Congress.jpg/220px-Peter_DeFazio,_Official_Portrait,_112th_Congress.jpg');

-- Create office holders for the State offices
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Kurt','','Schrader','D','https://beta.congress.gov/img/member/112_rp_or_5_schrader_kurt.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('John','','Kitzhaber','D','http://www.oregon.gov/gov/PublishingImages/kitzhaber_244x241.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Ellen','','Rosenblum','D', 'http://ellenrosenblum.com/images/Ellen-Rosenblum.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Kate','','Brown','D','http://sos.oregon.gov/PublishingImages/kate-brown-headshot-2012-color-275x347.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Brad','','Avakian','D','http://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Brad_Avakian_2008_Color.jpg/220px-Brad_Avakian_2008_Color.jpg');
INSERT INTO office_holder (first_name, middle_name, last_name, party_affiliation, photo_link)
VALUES ('Ted','','Wheeler','D','http://mediad.publicbroadcasting.net/p/klcc/files/201408/TEDWHEELER.jpg');

-- Create Federal offices
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('President of the United States', 1, 'The President is both the head of state and head of government of the United States of America, and Commander-in-Chief of the armed forces. Under Article II of the Constitution, the President is responsible for the execution and enforcement of the laws created by Congress. Fifteen executive departments — each led by an appointed member of the President''s Cabinet — carry out the day-to-day administration of the federal government. They are joined in this by other executive agencies such as the CIA and Environmental Protection Agency, the heads of which are not part of the Cabinet, but who are under the full authority of the President. The President also appoints the heads of more than 50 independent federal commissions, such as the Federal Reserve Board or the Securities and Exchange Commission, as well as federal judges, ambassadors, and other federal offices. The Executive Office of the President (EOP) consists of the immediate staff to the President, along with entities such as the Office of Management and Budget and the Office of the United States Trade Representative.', 48, 400000);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('US Senator', 2, 'The primary function of the Senate is to make laws, ratify treaties, represent their home states and check the power of the president.', 72, 174000);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('US Representative', 5, 'As per the Constitution, the U.S. House of Representatives makes and passes federal laws. The House is one of Congress''s two chambers (the other is the U.S. Senate), and part of the federal government''s legislative branch. The number of voting representatives in the House is fixed by law at no more than 435, proportionally representing the population of the 50 states.', 24, 174000);

-- Create State offices
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('Governor', 1, 'Chief executive of the state of Oregon', 48, 93600);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('Secretary of State', 1, 'an elected constitutional officer within the executive branch of government of the U.S. state of Oregon, is first in line of succession to the Governor. The duties of office are: auditor of public accounts, chief elections officer, and administrator of public records. Additionally, the Secretary of State serves on the Oregon State Land Board and chairs the Oregon Sustainability Board. Following every United States Census, if the Oregon Legislative Assembly cannot come to agreement over changes to legislative redistricting, the duty falls to the Secretary of State.', 48, 0);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('State Treasurer', 1, 'The Office of the State Treasurer is a sophisticated organization with a wide range of financial responsibilities, including managing the investment of state funds, issuing all state bonds, serving as the central bank for state agencies, and administering the Oregon 529 College Savings Network. The State Treasury seeks to provide the highest value to taxpayers by protecting public funds and earning strong investment returns -- and by being an efficient, transparent and accountable organization.', 48, 72000);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('State Attorney General', 1, 'The Attorney General represents the state of Oregon in all court actions and other legal proceedings in which it is a party or has an interest. They also conduct all legal business of state departments, boards and commissions that require legal counsel. Ballot titles for measures in Oregon elections are written by the Attorney General, who also and appoints the assistant attorneys general who serve as counsel to the various state departments, boards and commissions. The Attorney General provides written opinions upon any question of law in which any government entity within the state may have an interest when requested by the governor, any state agency official or any member of the legislature, but is prohibited by law from rendering opinions or giving legal advice to any other persons or agencies.', 48, 77200);
INSERT INTO office (title, num_positions, responsibilities, term_length_months, salary)
VALUES ('State Commissioner of Labor & Industries', 1, 'The Commissioner serves as chief executive of the department-level Oregon Bureau of Labor and Industries, chairs the State Apprenticeship and Training Council, and acts as executive secretary of the Wage and Hour Commission. He or she has enforcement responsibility for state laws prohibiting discrimination in employment, housing, public accommodation, and vocational, professional and trade schools, and may initiate a “commissioner''s complaint” on behalf of victims. The Commissioner administers state laws regulating wages, hours of employment, basic working conditions, child labor and wage rates; and is responsible for licensure of certain professions and industries. Final orders in contested cases are issued by the commissioner. The Wage Security Fund that covers workers for unpaid wages in certain business closure situations, and enforcement of group-health insurance termination-notification provisions fall within the Commissioner''s purview. He or she is also responsible for oversight of the state’s registered apprenticeship-training system.', 48, 72000 );

-- Create office positions that lookup the right data for the foreign key fields
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'President of the United States'),
    (SELECT id FROM office_holder WHERE first_name = 'Barak' AND last_name = 'Obama'),
    '', 0, '1/20/2013', '1/20/2017');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Senator'),
    (SELECT id FROM office_holder WHERE first_name = 'Ron' AND last_name = 'Wyden'),
    '', 1, '1/3/2011', '1/3/2017');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Senator'),
    (SELECT id FROM office_holder WHERE first_name = 'Jeff' AND last_name = 'Merkley'),
    '', 2, '1/3/2009', '1/3/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Representative'),
    (SELECT id FROM office_holder WHERE first_name = 'Suzanne' AND last_name = 'Bonamici'),
    '1st District', 5, '1/3/2013', '1/3/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Representative'),
    (SELECT id FROM office_holder WHERE first_name = 'Greg' AND last_name = 'Walden'),
    '2nd District', 6, '1/3/2013', '1/3/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Representative'),
    (SELECT id FROM office_holder WHERE first_name = 'Earl' AND last_name = 'Blumenauer'),
    '3rd District', 7, '1/3/2013', '1/3/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Representative'),
    (SELECT id FROM office_holder WHERE first_name = 'Peter' AND last_name = 'Defazio'),
    '4th District', 8, '1/3/2013', '1/3/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for Federal Positions'),
    (SELECT id FROM office WHERE title = 'US Representative'),
    (SELECT id FROM office_holder WHERE first_name = 'Kurt' AND last_name = 'Schrader'),
    '5th District', 9, '1/3/2013', '1/3/2015');

-- Statewide positions
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for State Positions'),
    (SELECT id FROM office WHERE title = 'Governor'),
    (SELECT id FROM office_holder WHERE first_name = 'John' AND last_name = 'Kitzhaber'),
    '', 0, '1/20/2011', '1/20/2015');

INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for State Positions'),
    (SELECT id FROM office WHERE title = 'Secretary of State'),
    (SELECT id FROM office_holder WHERE first_name = 'Kate' AND last_name = 'Brown'),
    '', 0, '1/20/2011', '1/20/2015');

INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for State Positions'),
    (SELECT id FROM office WHERE title = 'State Attorney General'),
    (SELECT id FROM office_holder WHERE first_name = 'Ellen' AND last_name = 'Rosenblum'),
    '', 0, '1/20/2011', '1/20/2015');

INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for State Positions'),
    (SELECT id FROM office WHERE title = 'State Treasurer'),
    (SELECT id FROM office_holder WHERE first_name = 'Ted' AND last_name = 'Wheeler'),
    '', 0, '1/20/2011', '1/20/2015');
INSERT INTO office_position (district_id, office_id, office_holder_id, position_name, office_rank, term_start, term_end)
VALUES (
    (SELECT id FROM district WHERE name = 'State of Oregon for State Positions'),
    (SELECT id FROM office WHERE title = 'State Commissioner of Labor & Industries'),
    (SELECT id FROM office_holder WHERE first_name = 'Brad' AND last_name = 'Avakian'),
    '', 0, '1/20/2011', '1/20/2015');


