USE bookandboard;

SET NAMES utf8mb4;

INSERT IGNORE INTO users (id, name, email, phone, password_hash, role)
VALUES
  (1, 'Priya Raman', 'staff@bookandboard.co.uk', '', '$argon2id$v=19$m=65536,t=4,p=1$aFJYS2R2ZnlXSW9SQ0hGSw$GpEKUDk7vAL9vSJJ6Vbez2+KzzuDZeE4ZlCK7fDsvlE', 'staff'),
  (2, 'Sam Whitfield', 'demo@bookandboard.co.uk', '07700 900123', '$argon2id$v=19$m=65536,t=4,p=1$L0dIbU9VWDd1ZDhRcGNsUw$gUwO6WalJbS9d6MF7naFINeM3nRZAdiWK3E9RPSyR7U', 'customer');

INSERT IGNORE INTO offers (id, title, destination, type, description, price, start_date, end_date, image, alt, active, bestseller)
VALUES
  (1, 'Venetian Long Weekend', 'Venice, Italy', 'Travel and Hotel Package', 'Four nights beside the Grand Canal, with a Rialto hotel, breakfast included and return flights from Gatwick.', 749, '2026-10-12', '2026-10-16', 'assets/img/offer-venice.jpg', 'Gondolas on the Grand Canal in front of the Rialto Bridge, Venice.', 1, 1),
  (2, 'Caldera Week', 'Santorini, Greece', 'Travel and Hotel Package', 'Seven nights half board in Oia, with a caldera view room, airport transfers and return flights from Manchester.', 899, '2026-09-03', '2026-09-10', 'assets/img/offer-santorini.jpg', 'Whitewashed steps leading down to the blue Aegean sea in Santorini.', 1, 1),
  (3, 'Andaman All Inclusive', 'Krabi, Thailand', 'Complete Holiday Package', 'Ten nights all inclusive at a beachfront resort, with private transfers, car hire for the week, return flights from Heathrow and travel insurance for two.', 1349, '2026-11-14', '2026-11-24', 'assets/img/offer-krabi.jpg', 'A palm fringed resort pool beneath limestone hills in Krabi, Thailand.', 1, 1),
  (4, 'Paris by Eurostar', 'Paris, France', 'Travel Plan', 'Return Eurostar from St Pancras and a three day travel pass for the metro. Travel only, so you can stay where you like: tell us and we will add a hotel.', 189, '2027-02-20', '2027-02-23', 'assets/img/hero-paris.jpg', 'The Pont Alexandre III in Paris at dusk, its lamps lit along the Seine.', 1, 0);

INSERT IGNORE INTO branches (id, name, location, street, area, phone, tel, email, hours, is_head_office)
VALUES
  (1, 'Manchester', 'City centre, on Harbrook Row', '18 Harbrook Row', 'Manchester M3 4CV', '0161 496 0117', '+441614960117', 'manchester@bookandboard.co.uk', 'Monday to Saturday, 9am to 5.30pm', 0),
  (2, 'Birmingham', 'City centre, on Calderley Parade', '7 Calderley Parade', 'Birmingham B3 6KO', '0121 496 0119', '+441214960119', 'birmingham@bookandboard.co.uk', 'Monday to Saturday, 9am to 5.30pm', 0),
  (3, 'Glasgow', 'City centre, on Rowanbank Court', '31 Rowanbank Court', 'Glasgow G1 5MC', '0141 496 0121', '+441414960121', 'glasgow@bookandboard.co.uk', 'Monday to Saturday, 9am to 5.30pm', 0),
  (4, 'Bristol', 'City centre, on Pennfield Walk', '24 Pennfield Walk', 'Bristol BS1 7VC', '0117 496 0123', '+441174960123', 'bristol@bookandboard.co.uk', 'Monday to Saturday, 9am to 5.30pm', 0),
  (5, 'London', 'City centre, on Thorncroft Place', '9 Thorncroft Place', 'London EC4M 3CK', '020 7946 0114', '+442079460114', 'hello@bookandboard.co.uk', 'Monday to Friday, 9am to 5.30pm', 1);

INSERT IGNORE INTO flights (id, airline, origin, destination, departure_date, departure_time, arrival_time, duration_minutes, stops, price)
VALUES
  (1, 'Britannia Air', 'London Heathrow', 'Paris, France', '2026-10-12', '07:35:00', '09:50:00', 75, 0, 98),
  (2, 'Cityhopper', 'Manchester', 'Paris, France', '2026-10-12', '11:20:00', '13:55:00', 95, 0, 126),
  (3, 'Northern Wing', 'Glasgow', 'Paris, France', '2026-10-12', '06:15:00', '11:40:00', 265, 1, 152),
  (4, 'Britannia Air', 'London Heathrow', 'Paris, France', '2026-10-13', '18:05:00', '20:20:00', 75, 0, 142),
  (5, 'Cityhopper', 'Bristol', 'Paris, France', '2026-10-14', '09:40:00', '12:05:00', 85, 0, 115),
  (6, 'Britannia Air', 'London Heathrow', 'Venice, Italy', '2026-10-12', '08:10:00', '11:25:00', 135, 0, 164),
  (7, 'Cityhopper', 'Birmingham', 'Venice, Italy', '2026-10-12', '13:45:00', '17:20:00', 155, 0, 188),
  (8, 'Northern Wing', 'Bristol', 'Venice, Italy', '2026-10-12', '06:50:00', '14:55:00', 425, 1, 143),
  (9, 'Britannia Air', 'London Heathrow', 'Venice, Italy', '2026-10-13', '16:30:00', '19:45:00', 135, 0, 176),
  (10, 'Northern Wing', 'Glasgow', 'Venice, Italy', '2026-10-14', '07:05:00', '16:40:00', 515, 2, 131),
  (11, 'Aer Meridian', 'London Heathrow', 'Santorini, Greece', '2026-09-03', '09:25:00', '15:30:00', 245, 0, 236),
  (12, 'Aer Meridian', 'Manchester', 'Santorini, Greece', '2026-09-03', '07:00:00', '18:20:00', 560, 1, 198),
  (13, 'Northern Wing', 'Glasgow', 'Santorini, Greece', '2026-09-03', '06:30:00', '21:15:00', 765, 2, 174),
  (14, 'Aer Meridian', 'London Heathrow', 'Santorini, Greece', '2026-09-05', '14:10:00', '20:15:00', 245, 0, 259),
  (15, 'Gulf Crossing', 'London Heathrow', 'Krabi, Thailand', '2026-11-14', '21:40:00', '22:25:00', 945, 1, 612),
  (16, 'Gulf Crossing', 'Manchester', 'Krabi, Thailand', '2026-11-14', '20:05:00', '23:50:00', 1065, 1, 588),
  (17, 'Northern Wing', 'Glasgow', 'Krabi, Thailand', '2026-11-14', '16:20:00', '20:50:00', 1290, 2, 544),
  (18, 'Gulf Crossing', 'London Heathrow', 'Krabi, Thailand', '2026-11-15', '12:15:00', '13:30:00', 1035, 1, 675),
  (19, 'Britannia Air', 'London Heathrow', 'Barcelona, Spain', '2026-10-12', '07:15:00', '10:20:00', 125, 0, 89),
  (20, 'Cityhopper', 'Birmingham', 'Barcelona, Spain', '2026-10-12', '15:30:00', '18:50:00', 140, 0, 104),
  (21, 'Northern Wing', 'Glasgow', 'Barcelona, Spain', '2026-10-13', '08:45:00', '15:10:00', 325, 1, 132),
  (22, 'Cityhopper', 'Bristol', 'Barcelona, Spain', '2026-10-14', '11:00:00', '14:15:00', 135, 0, 97),
  (23, 'Atlantic Star', 'London Heathrow', 'New York, USA', '2026-11-05', '10:30:00', '13:35:00', 485, 0, 398),
  (24, 'Atlantic Star', 'Manchester', 'New York, USA', '2026-11-05', '09:50:00', '14:45:00', 535, 0, 445),
  (25, 'Northern Wing', 'Glasgow', 'New York, USA', '2026-11-05', '07:20:00', '18:30:00', 670, 1, 352),
  (26, 'Atlantic Star', 'London Heathrow', 'New York, USA', '2026-11-06', '16:45:00', '19:55:00', 490, 0, 429);

INSERT IGNORE INTO hotels (id, name, destination, description, available_from, available_to, price_per_night, rating, image, alt)
VALUES
  (1, 'Hôtel Rive Gauche', 'Paris, France', 'A converted townhouse on a quiet street in the 6th, ten minutes on foot from the Musée d\'Orsay. Breakfast is served in the courtyard.', '2026-09-01', '2026-12-20', 142, 4.5, 'assets/img/hero-paris.jpg', 'The Pont Alexandre III in Paris at dusk, its lamps lit along the Seine.'),
  (2, 'Le Marais Maison', 'Paris, France', 'Eighteen rooms above a bakery in the Marais, with the Picasso museum and the Place des Vosges at the end of the street.', '2026-09-01', '2027-01-10', 98, 4.1, '', ''),
  (3, 'Gare du Nord Lodge', 'Paris, France', 'Straightforward rooms two minutes from the Eurostar platforms, useful if you are arriving late or leaving early.', '2026-08-01', '2026-11-30', 74, 3.6, '', ''),
  (4, 'Ca\' del Ponte', 'Venice, Italy', 'A canal-side palazzo a bridge away from the Rialto market, with breakfast on the water terrace and a private landing stage.', '2026-09-15', '2026-11-15', 186, 4.7, 'assets/img/offer-venice.jpg', 'Gondolas on the Grand Canal in front of the Rialto Bridge, Venice.'),
  (5, 'Cannaregio Guesthouse', 'Venice, Italy', 'A family-run guesthouse in the quietest sestiere, twenty minutes\' walk from St Mark\'s and away from the crowds.', '2026-09-01', '2026-12-31', 108, 4, '', ''),
  (6, 'Mestre Station Rooms', 'Venice, Italy', 'Budget rooms on the mainland with a ten minute train into Santa Lucia, for travellers who would rather spend the money on the city itself.', '2026-08-01', '2027-02-28', 62, 3.4, '', ''),
  (7, 'Caldera View Suites', 'Santorini, Greece', 'Cave suites cut into the cliff at Oia, each with a private terrace and a plunge pool facing the volcano and the sunset.', '2026-08-15', '2026-10-31', 214, 4.8, 'assets/img/offer-santorini.jpg', 'Whitewashed steps leading down to the blue Aegean sea in Santorini.'),
  (8, 'Fira Courtyard Rooms', 'Santorini, Greece', 'Half board in the middle of Fira, on the bus route for the beaches and a short walk from the cable car down to the old port.', '2026-08-15', '2026-11-05', 132, 4.2, '', ''),
  (9, 'Andaman Bay Resort', 'Krabi, Thailand', 'All inclusive beachfront rooms beneath the limestone hills, with two pools, a dive school and longtail boats to the islands from the beach.', '2026-10-01', '2027-03-31', 96, 4.6, 'assets/img/offer-krabi.jpg', 'A palm fringed resort pool beneath limestone hills in Krabi, Thailand.'),
  (10, 'Railay Garden Lodge', 'Krabi, Thailand', 'Wooden bungalows in a garden behind Railay East, reached only by boat, with the climbing walls a few minutes away.', '2026-10-01', '2027-04-30', 58, 4, '', ''),
  (11, 'Eixample Rooms', 'Barcelona, Spain', 'A modernista building on Carrer de Mallorca with tiled floors and a roof terrace, five minutes from the Sagrada Família.', '2026-09-01', '2026-12-15', 112, 4.3, '', ''),
  (12, 'Barceloneta Beach Hotel', 'Barcelona, Spain', 'Simple sea-facing rooms a street back from the sand, with the fish restaurants of the old fishermen\'s quarter on the doorstep.', '2026-08-01', '2026-11-30', 88, 3.9, '', ''),
  (13, 'Midtown Union', 'New York, USA', 'A tower on West 45th with rooms above the twentieth floor, two blocks from Grand Central and the theatre district.', '2026-10-01', '2027-01-31', 198, 4.4, '', ''),
  (14, 'Brooklyn Heights Inn', 'New York, USA', 'A brownstone on a tree-lined street with the promenade and the Manhattan skyline at the end of the block, one stop from Wall Street.', '2026-10-01', '2027-02-28', 156, 4, '', '');

INSERT IGNORE INTO previous_packages (id, customer_id, title, destination, start_date, end_date, package_type, status)
VALUES
  (5, 2, 'Amalfi Coast Escape', 'Sorrento, Italy', '2024-05-11', '2024-05-18', 'Complete Holiday Package', 'Completed'),
  (6, 2, 'Lisbon City Break', 'Lisbon, Portugal', '2025-03-07', '2025-03-10', 'Travel and Hotel Package', 'Completed'),
  (7, 2, 'Edinburgh by Rail', 'Edinburgh, Scotland', '2025-11-21', '2025-11-23', 'Travel Plan', 'Completed'),
  (8, 2, 'Venetian Long Weekend', 'Venice, Italy', '2026-10-12', '2026-10-16', 'Travel and Hotel Package', 'Upcoming');
