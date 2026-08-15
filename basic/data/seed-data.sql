USE bookandboard_basic;

SET NAMES utf8mb4;

INSERT IGNORE INTO users (id, name, email, phone, password_hash, role)
VALUES
  (1, 'Priya Raman', 'staff@bookandboard.co.uk', '', '$argon2id$v=19$m=65536,t=4,p=1$aFJYS2R2ZnlXSW9SQ0hGSw$GpEKUDk7vAL9vSJJ6Vbez2+KzzuDZeE4ZlCK7fDsvlE', 'staff');

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

