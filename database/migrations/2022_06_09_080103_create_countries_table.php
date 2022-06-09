<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('dial', 3);
            $table->string('country_abbr', 32);
            $table->string('currency_name', 32);
            $table->string('country_short', 64);
            $table->string('currency_code', 8);
            $table->string('currency_country_name', 64);
            $table->string('name', 64);
        });

        DB::insert("INSERT INTO `countries` (`id`, `dial`, `country_abbr`, `currency_name`, `country_short`, `currency_code`, `currency_country_name`, `name`) VALUES
        (2, '886', 'TW', 'New Taiwan dollar', 'Taiwan', 'TWD', '', 'Taiwan'),
        (3, '93', 'AF', 'Afghani', 'Afghanistan', 'AFN', 'AFGHANISTAN', 'Afghanistan'),
        (4, '355', 'AL', 'Lek', 'Albania', 'ALL', 'ALBANIA', 'Albania'),
        (5, '213', 'DZ', 'Algerian Dinar', 'Algeria', 'DZD', 'ALGERIA', 'Algeria'),
        (7, '376', 'AD', 'Euro', 'Andorra', 'EUR', 'ANDORRA', 'Andorra'),
        (8, '244', 'AO', 'Kwanza', 'Angola', 'AOA', 'ANGOLA', 'Angola'),
        (10, '672', 'AQ', 'No universal currency', '', '', 'ANTARCTICA', 'Antarctica'),
        (12, '54', 'AR', 'Argentine Peso', 'Argentina', 'ARS', 'ARGENTINA', 'Argentina'),
        (13, '374', 'AM', 'Armenian Dram', 'Armenia', 'AMD', 'ARMENIA', 'Armenia'),
        (14, '297', 'AW', 'Aruban Florin', '', 'AWG', 'ARUBA', 'Aruba'),
        (15, '61', 'AU', 'Australian Dollar', 'Australia', 'AUD', 'AUSTRALIA', 'Australia'),
        (16, '43', 'AT', 'Euro', 'Austria', 'EUR', 'AUSTRIA', 'Austria'),
        (17, '994', 'AZ', 'Azerbaijan Manat', 'Azerbaijan', 'AZN', 'AZERBAIJAN', 'Azerbaijan'),
        (19, '973', 'BH', 'Bahraini Dinar', 'Bahrain', 'BHD', 'BAHRAIN', 'Bahrain'),
        (20, '880', 'BD', 'Taka', 'Bangladesh', 'BDT', 'BANGLADESH', 'Bangladesh'),
        (22, '375', 'BY', 'Belarusian Ruble', 'Belarus', 'BYN', 'BELARUS', 'Belarus'),
        (23, '32', 'BE', 'Euro', 'Belgium', 'EUR', 'BELGIUM', 'Belgium'),
        (24, '501', 'BZ', 'Belize Dollar', 'Belize', 'BZD', 'BELIZE', 'Belize'),
        (25, '229', 'BJ', 'CFA Franc BCEAO', 'Benin', 'XOF', 'BENIN', 'Benin'),
        (27, '975', 'BT', 'Indian Rupee,Ngultrum', 'Bhutan', 'INR,BTN', 'BHUTAN', 'Bhutan'),
        (28, '591', 'BO', 'Boliviano', 'Bolivia (Plurinational State of)', 'BOB', 'BOLIVIA (PLURINATIONAL STATE OF)', 'Bolivia'),
        (29, '599', 'BQ', 'US Dollar', '', 'USD', 'BONAIRE, SINT EUSTATIUS AND SABA', 'Caribbean Netherlands'),
        (30, '387', 'BA', 'Convertible Mark', 'Bosnia and Herzegovina', 'BAM', 'BOSNIA AND HERZEGOVINA', 'Bosnia'),
        (31, '267', 'BW', 'Pula', 'Botswana', 'BWP', 'BOTSWANA', 'Botswana'),
        (32, '47', 'BV', 'Norwegian Krone', '', 'NOK', 'BOUVET ISLAND', 'Bouvet Island'),
        (33, '55', 'BR', 'Brazilian Real', 'Brazil', 'BRL', 'BRAZIL', 'Brazil'),
        (34, '246', 'IO', 'US Dollar', '', 'USD', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory'),
        (36, '673', 'BN', 'Brunei Dollar', 'Brunei Darussalam', 'BND', 'BRUNEI DARUSSALAM', 'Brunei'),
        (37, '359', 'BG', 'Bulgarian Lev', 'Bulgaria', 'BGN', 'BULGARIA', 'Bulgaria'),
        (38, '226', 'BF', 'CFA Franc BCEAO', 'Burkina Faso', 'XOF', 'BURKINA FASO', 'Burkina Faso'),
        (39, '257', 'BI', 'Burundi Franc', 'Burundi', 'BIF', 'BURUNDI', 'Burundi'),
        (40, '238', 'CV', 'Cabo Verde Escudo', 'Cabo Verde', 'CVE', 'CABO VERDE', 'Cape Verde'),
        (41, '855', 'KH', 'Riel', 'Cambodia', 'KHR', 'CAMBODIA', 'Cambodia'),
        (42, '237', 'CM', 'CFA Franc BEAC', 'Cameroon', 'XAF', 'CAMEROON', 'Cameroon'),
        (43, '1', 'CA', 'Canadian Dollar', 'Canada', 'CAD', 'CANADA', 'Canada'),
        (45, '236', 'CF', 'CFA Franc BEAC', 'Central African Republic (the)', 'XAF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic'),
        (46, '235', 'TD', 'CFA Franc BEAC', 'Chad', 'XAF', 'CHAD', 'Chad'),
        (47, '56', 'CL', 'Chilean Peso', 'Chile', 'CLP', 'CHILE', 'Chile'),
        (48, '86', 'CN', 'Yuan Renminbi', 'China', 'CNY', 'CHINA', 'China'),
        (49, '852', 'HK', 'Hong Kong Dollar', '', 'HKD', 'HONG KONG', 'Hong Kong'),
        (50, '853', 'MO', 'Pataca', '', 'MOP', 'MACAO', 'Macau'),
        (51, '61', 'CX', 'Australian Dollar', '', 'AUD', 'CHRISTMAS ISLAND', 'Christmas Island'),
        (52, '61', 'CC', 'Australian Dollar', '', 'AUD', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands'),
        (53, '57', 'CO', 'Colombian Peso', 'Colombia', 'COP', 'COLOMBIA', 'Colombia'),
        (54, '269', 'KM', 'Comorian Franc ', 'Comoros (the)', 'KMF', 'COMOROS', 'Comoros'),
        (55, '242', 'CG', 'CFA Franc BEAC', 'Congo (the)', 'XAF', 'CONGO', 'Congo - Brazzaville'),
        (56, '682', 'CK', 'New Zealand Dollar', 'Cook Islands (the)    **', 'NZD', 'COOK ISLANDS', 'Cook Islands'),
        (57, '506', 'CR', 'Costa Rican Colon', 'Costa Rica', 'CRC', 'COSTA RICA', 'Costa Rica'),
        (58, '385', 'HR', 'Kuna', 'Croatia', 'HRK', 'CROATIA', 'Croatia'),
        (59, '53', 'CU', 'Cuban Peso,Peso Convertible', 'Cuba', 'CUP,CUC', 'CUBA', 'Cuba'),
        (60, '599', 'CW', 'Netherlands Antillean Guilder', '', 'ANG', 'CURAÇAO', 'Curaçao'),
        (61, '357', 'CY', 'Euro', 'Cyprus', 'EUR', 'CYPRUS', 'Cyprus'),
        (62, '420', 'CZ', 'Czech Koruna', 'Czech Republic (the)', 'CZK', 'CZECHIA', 'Czechia'),
        (63, '225', 'CI', 'CFA Franc BCEAO', 'Côte d\'Ivoire', 'XOF', 'CÔTE D\'IVOIRE', 'Côte d’Ivoire'),
        (64, '850', 'KP', 'North Korean Won', 'Democratic People\'s Republic of Korea (the)', 'KPW', 'KOREA (THE DEMOCRATIC PEOPLE’S REPUBLIC OF)', 'North Korea'),
        (65, '243', 'CD', 'Congolese Franc', 'Democratic Republic of the Congo (the)', 'CDF', 'CONGO (THE DEMOCRATIC REPUBLIC OF THE)', 'Congo - Kinshasa'),
        (66, '45', 'DK', 'Danish Krone', 'Denmark', 'DKK', 'DENMARK', 'Denmark'),
        (67, '253', 'DJ', 'Djibouti Franc', 'Djibouti', 'DJF', 'DJIBOUTI', 'Djibouti'),
        (70, '593', 'EC', 'US Dollar', 'Ecuador', 'USD', 'ECUADOR', 'Ecuador'),
        (71, '20', 'EG', 'Egyptian Pound', 'Egypt', 'EGP', 'EGYPT', 'Egypt'),
        (72, '503', 'SV', 'El Salvador Colon,US Dollar', 'El Salvador', 'SVC,USD', 'EL SALVADOR', 'El Salvador'),
        (73, '240', 'GQ', 'CFA Franc BEAC', 'Equatorial Guinea', 'XAF', 'EQUATORIAL GUINEA', 'Equatorial Guinea'),
        (74, '291', 'ER', 'Nakfa', 'Eritrea', 'ERN', 'ERITREA', 'Eritrea'),
        (75, '372', 'EE', 'Euro', 'Estonia', 'EUR', 'ESTONIA', 'Estonia'),
        (76, '268', 'SZ', 'Lilangeni', '', 'SZL', 'ESWATINI', 'Eswatini'),
        (77, '251', 'ET', 'Ethiopian Birr', 'Ethiopia', 'ETB', 'ETHIOPIA', 'Ethiopia'),
        (78, '500', 'FK', '', '', '', '', 'Falkland Islands'),
        (79, '298', 'FO', 'Danish Krone', '', 'DKK', 'FAROE ISLANDS', 'Faroe Islands'),
        (80, '679', 'FJ', 'Fiji Dollar', 'Fiji', 'FJD', 'FIJI', 'Fiji'),
        (81, '358', 'FI', 'Euro', 'Finland', 'EUR', 'FINLAND', 'Finland'),
        (82, '33', 'FR', 'Euro', 'France', 'EUR', 'FRANCE', 'France'),
        (83, '594', 'GF', 'Euro', '', 'EUR', 'FRENCH GUIANA', 'French Guiana'),
        (84, '689', 'PF', 'CFP Franc', '', 'XPF', 'FRENCH POLYNESIA', 'French Polynesia'),
        (85, '262', 'TF', 'Euro', '', 'EUR', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories'),
        (86, '241', 'GA', 'CFA Franc BEAC', 'Gabon', 'XAF', 'GABON', 'Gabon'),
        (87, '220', 'GM', 'Dalasi', 'Gambia (the)', 'GMD', 'GAMBIA', 'Gambia'),
        (88, '995', 'GE', 'Lari', 'Georgia', 'GEL', 'GEORGIA', 'Georgia'),
        (89, '49', 'DE', 'Euro', 'Germany', 'EUR', 'GERMANY', 'Germany'),
        (90, '233', 'GH', 'Ghana Cedi', 'Ghana', 'GHS', 'GHANA', 'Ghana'),
        (91, '350', 'GI', 'Gibraltar Pound', '', 'GIP', 'GIBRALTAR', 'Gibraltar'),
        (92, '30', 'GR', 'Euro', 'Greece', 'EUR', 'GREECE', 'Greece'),
        (93, '299', 'GL', 'Danish Krone', '', 'DKK', 'GREENLAND', 'Greenland'),
        (95, '590', 'GP', 'Euro', '', 'EUR', 'GUADELOUPE', 'Guadeloupe'),
        (97, '502', 'GT', 'Quetzal', 'Guatemala', 'GTQ', 'GUATEMALA', 'Guatemala'),
        (98, '44', 'GG', 'Pound Sterling', '', 'GBP', 'GUERNSEY', 'Guernsey'),
        (99, '224', 'GN', 'Guinean Franc', 'Guinea', 'GNF', 'GUINEA', 'Guinea'),
        (100, '245', 'GW', 'CFA Franc BCEAO', 'Guinea-Bissau', 'XOF', 'GUINEA-BISSAU', 'Guinea-Bissau'),
        (101, '592', 'GY', 'Guyana Dollar', 'Guyana', 'GYD', 'GUYANA', 'Guyana'),
        (102, '509', 'HT', 'Gourde,US Dollar', 'Haiti', 'HTG,USD', 'HAITI', 'Haiti'),
        (103, '672', 'HM', 'Australian Dollar', '', 'AUD', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard & McDonald Islands'),
        (105, '504', 'HN', 'Lempira', 'Honduras', 'HNL', 'HONDURAS', 'Honduras'),
        (106, '36', 'HU', 'Forint', 'Hungary', 'HUF', 'HUNGARY', 'Hungary'),
        (107, '354', 'IS', 'Iceland Krona', 'Iceland', 'ISK', 'ICELAND', 'Iceland'),
        (108, '91', 'IN', 'Indian Rupee', 'India', 'INR', 'INDIA', 'India'),
        (109, '62', 'ID', 'Rupiah', 'Indonesia', 'IDR', 'INDONESIA', 'Indonesia'),
        (110, '98', 'IR', 'Iranian Rial', 'Iran (Islamic Republic of)', 'IRR', 'IRAN (ISLAMIC REPUBLIC OF)', 'Iran'),
        (111, '964', 'IQ', 'Iraqi Dinar', 'Iraq', 'IQD', 'IRAQ', 'Iraq'),
        (112, '353', 'IE', 'Euro', 'Ireland', 'EUR', 'IRELAND', 'Ireland'),
        (113, '44', 'IM', 'Pound Sterling', '', 'GBP', 'ISLE OF MAN', 'Isle of Man'),
        (114, '972', 'IL', 'New Israeli Sheqel', 'Israel', 'ILS', 'ISRAEL', 'Israel'),
        (115, '39', 'IT', 'Euro', 'Italy', 'EUR', 'ITALY', 'Italy'),
        (117, '81', 'JP', 'Yen', 'Japan', 'JPY', 'JAPAN', 'Japan'),
        (118, '44', 'JE', 'Pound Sterling', '', 'GBP', 'JERSEY', 'Jersey'),
        (119, '962', 'JO', 'Jordanian Dinar', 'Jordan', 'JOD', 'JORDAN', 'Jordan'),
        (120, '7', 'KZ', 'Tenge', 'Kazakhstan', 'KZT', 'KAZAKHSTAN', 'Kazakhstan'),
        (121, '254', 'KE', 'Kenyan Shilling', 'Kenya', 'KES', 'KENYA', 'Kenya'),
        (122, '686', 'KI', 'Australian Dollar', 'Kiribati', 'AUD', 'KIRIBATI', 'Kiribati'),
        (123, '965', 'KW', 'Kuwaiti Dinar', 'Kuwait', 'KWD', 'KUWAIT', 'Kuwait'),
        (124, '996', 'KG', 'Som', 'Kyrgyzstan', 'KGS', 'KYRGYZSTAN', 'Kyrgyzstan'),
        (125, '856', 'LA', 'Lao Kip', 'Lao People\'s Democratic Republic (the)', 'LAK', 'LAO PEOPLE’S DEMOCRATIC REPUBLIC', 'Laos'),
        (126, '371', 'LV', 'Euro', 'Latvia', 'EUR', 'LATVIA', 'Latvia'),
        (127, '961', 'LB', 'Lebanese Pound', 'Lebanon', 'LBP', 'LEBANON', 'Lebanon'),
        (128, '266', 'LS', 'Loti,Rand', 'Lesotho', 'LSL,ZAR', 'LESOTHO', 'Lesotho'),
        (129, '231', 'LR', 'Liberian Dollar', 'Liberia', 'LRD', 'LIBERIA', 'Liberia'),
        (130, '218', 'LY', 'Libyan Dinar', 'Libya', 'LYD', 'LIBYA', 'Libya'),
        (131, '423', 'LI', 'Swiss Franc', 'Liechtenstein', 'CHF', 'LIECHTENSTEIN', 'Liechtenstein'),
        (132, '370', 'LT', 'Euro', 'Lithuania', 'EUR', 'LITHUANIA', 'Lithuania'),
        (133, '352', 'LU', 'Euro', 'Luxembourg', 'EUR', 'LUXEMBOURG', 'Luxembourg'),
        (134, '261', 'MG', 'Malagasy Ariary', 'Madagascar', 'MGA', 'MADAGASCAR', 'Madagascar'),
        (135, '265', 'MW', 'Malawi Kwacha', 'Malawi', 'MWK', 'MALAWI', 'Malawi'),
        (136, '60', 'MY', 'Malaysian Ringgit', 'Malaysia', 'MYR', 'MALAYSIA', 'Malaysia'),
        (137, '960', 'MV', 'Rufiyaa', 'Maldives', 'MVR', 'MALDIVES', 'Maldives'),
        (138, '223', 'ML', 'CFA Franc BCEAO', 'Mali', 'XOF', 'MALI', 'Mali'),
        (139, '356', 'MT', 'Euro', 'Malta', 'EUR', 'MALTA', 'Malta'),
        (140, '692', 'MH', 'US Dollar', 'Marshall Islands (the)', 'USD', 'MARSHALL ISLANDS', 'Marshall Islands'),
        (141, '596', 'MQ', 'Euro', '', 'EUR', 'MARTINIQUE', 'Martinique'),
        (142, '222', 'MR', 'Ouguiya', 'Mauritania', 'MRU', 'MAURITANIA', 'Mauritania'),
        (143, '230', 'MU', 'Mauritius Rupee', 'Mauritius', 'MUR', 'MAURITIUS', 'Mauritius'),
        (144, '262', 'YT', 'Euro', '', 'EUR', 'MAYOTTE', 'Mayotte'),
        (145, '52', 'MX', 'Mexican Peso', 'Mexico', 'MXN', 'MEXICO', 'Mexico'),
        (146, '691', 'FM', 'US Dollar', 'Micronesia (Federated States of)', 'USD', 'MICRONESIA (FEDERATED STATES OF)', 'Micronesia'),
        (147, '377', 'MC', 'Euro', 'Monaco', 'EUR', 'MONACO', 'Monaco'),
        (148, '976', 'MN', 'Tugrik', 'Mongolia', 'MNT', 'MONGOLIA', 'Mongolia'),
        (149, '382', 'ME', 'Euro', 'Montenegro', 'EUR', 'MONTENEGRO', 'Montenegro'),
        (151, '212', 'MA', 'Moroccan Dirham', 'Morocco', 'MAD', 'MOROCCO', 'Morocco'),
        (152, '258', 'MZ', 'Mozambique Metical', 'Mozambique', 'MZN', 'MOZAMBIQUE', 'Mozambique'),
        (153, '95', 'MM', 'Kyat', 'Myanmar', 'MMK', 'MYANMAR', 'Myanmar'),
        (154, '264', 'NA', 'Namibia Dollar,Rand', 'Namibia', 'NAD,ZAR', 'NAMIBIA', 'Namibia'),
        (155, '674', 'NR', 'Australian Dollar', 'Nauru', 'AUD', 'NAURU', 'Nauru'),
        (156, '977', 'NP', 'Nepalese Rupee', 'Nepal', 'NPR', 'NEPAL', 'Nepal'),
        (157, '31', 'NL', 'Euro', 'Netherlands (the)', 'EUR', 'NETHERLANDS', 'Netherlands'),
        (158, '687', 'NC', 'CFP Franc', '', 'XPF', 'NEW CALEDONIA', 'New Caledonia'),
        (159, '64', 'NZ', 'New Zealand Dollar', 'New Zealand', 'NZD', 'NEW ZEALAND', 'New Zealand'),
        (160, '505', 'NI', 'Cordoba Oro', 'Nicaragua', 'NIO', 'NICARAGUA', 'Nicaragua'),
        (161, '227', 'NE', 'CFA Franc BCEAO', 'Niger (the)', 'XOF', 'NIGER', 'Niger'),
        (162, '234', 'NG', 'Naira', 'Nigeria', 'NGN', 'NIGERIA', 'Nigeria'),
        (163, '683', 'NU', 'New Zealand Dollar', 'Niue    **', 'NZD', 'NIUE', 'Niue'),
        (164, '672', 'NF', 'Australian Dollar', '', 'AUD', 'NORFOLK ISLAND', 'Norfolk Island'),
        (166, '47', 'NO', 'Norwegian Krone', 'Norway', 'NOK', 'NORWAY', 'Norway'),
        (167, '968', 'OM', 'Rial Omani', 'Oman', 'OMR', 'OMAN', 'Oman'),
        (168, '92', 'PK', 'Pakistan Rupee', 'Pakistan', 'PKR', 'PAKISTAN', 'Pakistan'),
        (169, '680', 'PW', 'US Dollar', 'Palau', 'USD', 'PALAU', 'Palau'),
        (170, '507', 'PA', 'Balboa,US Dollar', 'Panama', 'PAB,USD', 'PANAMA', 'Panama'),
        (171, '675', 'PG', 'Kina', 'Papua New Guinea', 'PGK', 'PAPUA NEW GUINEA', 'Papua New Guinea'),
        (172, '595', 'PY', 'Guarani', 'Paraguay', 'PYG', 'PARAGUAY', 'Paraguay'),
        (173, '51', 'PE', 'Sol', 'Peru', 'PEN', 'PERU', 'Peru'),
        (174, '63', 'PH', 'Philippine Peso', 'Philippines (the)', 'PHP', 'PHILIPPINES', 'Philippines'),
        (175, '870', 'PN', 'New Zealand Dollar', '', 'NZD', 'PITCAIRN', 'Pitcairn Islands'),
        (176, '48', 'PL', 'Zloty', 'Poland', 'PLN', 'POLAND', 'Poland'),
        (177, '351', 'PT', 'Euro', 'Portugal', 'EUR', 'PORTUGAL', 'Portugal'),
        (178, '1', 'PR', 'US Dollar', '', 'USD', 'PUERTO RICO', 'Puerto Rico'),
        (179, '974', 'QA', 'Qatari Rial', 'Qatar', 'QAR', 'QATAR', 'Qatar'),
        (180, '82', 'KR', 'Won', 'Republic of Korea (the)', 'KRW', 'KOREA (THE REPUBLIC OF)', 'South Korea'),
        (181, '373', 'MD', 'Moldovan Leu', 'Republic of Moldova (the)', 'MDL', 'MOLDOVA (THE REPUBLIC OF)', 'Moldova'),
        (182, '40', 'RO', 'Romanian Leu', 'Romania', 'RON', 'ROMANIA', 'Romania'),
        (183, '7', 'RU', 'Russian Ruble', 'Russian Federation (the)', 'RUB', 'RUSSIAN FEDERATION', 'Russia'),
        (184, '250', 'RW', 'Rwanda Franc', 'Rwanda', 'RWF', 'RWANDA', 'Rwanda'),
        (185, '262', 'RE', 'Euro', '', 'EUR', 'RÉUNION', 'Réunion'),
        (186, '590', 'BL', 'Euro', '', 'EUR', 'SAINT BARTHÉLEMY', 'St. Barthélemy'),
        (187, '290', 'SH', 'Saint Helena Pound', '', 'SHP', 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA', 'St. Helena'),
        (190, '590', 'MF', 'Euro', '', 'EUR', 'SAINT MARTIN (FRENCH PART)', 'St. Martin'),
        (191, '508', 'PM', 'Euro', '', 'EUR', 'SAINT PIERRE AND MIQUELON', 'St. Pierre & Miquelon'),
        (193, '685', 'WS', 'Tala', 'Samoa', 'WST', 'SAMOA', 'Samoa'),
        (194, '378', 'SM', 'Euro', 'San Marino', 'EUR', 'SAN MARINO', 'San Marino'),
        (195, '239', 'ST', 'Dobra', 'Sao Tome and Principe', 'STN', 'SAO TOME AND PRINCIPE', 'São Tomé & Príncipe'),
        (197, '966', 'SA', 'Saudi Riyal', 'Saudi Arabia', 'SAR', 'SAUDI ARABIA', 'Saudi Arabia'),
        (198, '221', 'SN', 'CFA Franc BCEAO', 'Senegal', 'XOF', 'SENEGAL', 'Senegal'),
        (199, '381', 'RS', 'Serbian Dinar', 'Serbia', 'RSD', 'SERBIA', 'Serbia'),
        (200, '248', 'SC', 'Seychelles Rupee', 'Seychelles', 'SCR', 'SEYCHELLES', 'Seychelles'),
        (201, '232', 'SL', 'Leone', 'Sierra Leone', 'SLL', 'SIERRA LEONE', 'Sierra Leone'),
        (202, '65', 'SG', 'Singapore Dollar', 'Singapore', 'SGD', 'SINGAPORE', 'Singapore'),
        (204, '421', 'SK', 'Euro', 'Slovakia', 'EUR', 'SLOVAKIA', 'Slovakia'),
        (205, '386', 'SI', 'Euro', 'Slovenia', 'EUR', 'SLOVENIA', 'Slovenia'),
        (206, '677', 'SB', 'Solomon Islands Dollar', 'Solomon Islands', 'SBD', 'SOLOMON ISLANDS', 'Solomon Islands'),
        (207, '252', 'SO', 'Somali Shilling', 'Somalia', 'SOS', 'SOMALIA', 'Somalia'),
        (208, '27', 'ZA', 'Rand', 'South Africa', 'ZAR', 'SOUTH AFRICA', 'South Africa'),
        (209, '500', 'GS', 'No universal currency', '', '', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia & South Sandwich Islands'),
        (210, '211', 'SS', 'South Sudanese Pound', 'South Sudan', 'SSP', 'SOUTH SUDAN', 'South Sudan'),
        (211, '34', 'ES', 'Euro', 'Spain', 'EUR', 'SPAIN', 'Spain'),
        (212, '94', 'LK', 'Sri Lanka Rupee', 'Sri Lanka', 'LKR', 'SRI LANKA', 'Sri Lanka'),
        (213, '970', 'PS', 'No universal currency', 'State of Palestine  *', '', 'PALESTINE, STATE OF', 'Palestine'),
        (214, '249', 'SD', 'Sudanese Pound', 'Sudan (the)', 'SDG', 'SUDAN', 'Sudan'),
        (215, '597', 'SR', 'Surinam Dollar', 'Suriname', 'SRD', 'SURINAME', 'Suriname'),
        (216, '47', 'SJ', 'Norwegian Krone', '', 'NOK', 'SVALBARD AND JAN MAYEN', 'Svalbard & Jan Mayen'),
        (217, '46', 'SE', 'Swedish Krona', 'Sweden', 'SEK', 'SWEDEN', 'Sweden'),
        (218, '41', 'CH', 'Swiss Franc', 'Switzerland', 'CHF', 'SWITZERLAND', 'Switzerland'),
        (219, '963', 'SY', 'Syrian Pound', 'Syrian Arab Republic', 'SYP', 'SYRIAN ARAB REPUBLIC', 'Syria'),
        (220, '992', 'TJ', 'Somoni', 'Tajikistan', 'TJS', 'TAJIKISTAN', 'Tajikistan'),
        (221, '66', 'TH', 'Baht', 'Thailand', 'THB', 'THAILAND', 'Thailand'),
        (222, '389', 'MK', 'Denar', 'the former Yugoslav Republic of Macedonia', 'MKD', 'MACEDONIA (THE FORMER YUGOSLAV REPUBLIC OF)', 'North Macedonia'),
        (223, '670', 'TL', 'US Dollar', 'Timor-Leste', 'USD', 'TIMOR-LESTE', 'Timor-Leste'),
        (224, '228', 'TG', 'CFA Franc BCEAO', 'Togo', 'XOF', 'TOGO', 'Togo'),
        (225, '690', 'TK', 'New Zealand Dollar', '', 'NZD', 'TOKELAU', 'Tokelau'),
        (226, '676', 'TO', 'Pa’anga', 'Tonga', 'TOP', 'TONGA', 'Tonga'),
        (228, '216', 'TN', 'Tunisian Dinar', 'Tunisia', 'TND', 'TUNISIA', 'Tunisia'),
        (229, '90', 'TR', 'Turkish Lira', 'Turkey', 'TRY', 'TURKEY', 'Turkey'),
        (230, '993', 'TM', 'Turkmenistan New Manat', 'Turkmenistan', 'TMT', 'TURKMENISTAN', 'Turkmenistan'),
        (232, '688', 'TV', 'Australian Dollar', 'Tuvalu', 'AUD', 'TUVALU', 'Tuvalu'),
        (233, '256', 'UG', 'Uganda Shilling', 'Uganda', 'UGX', 'UGANDA', 'Uganda'),
        (234, '380', 'UA', 'Hryvnia', 'Ukraine', 'UAH', 'UKRAINE', 'Ukraine'),
        (235, '971', 'AE', 'UAE Dirham', 'United Arab Emirates (the)', 'AED', 'UNITED ARAB EMIRATES', 'United Arab Emirates'),
        (236, '44', 'GB', 'Pound Sterling', 'United Kingdom of Great Britain and Northern Ireland (the)', 'GBP', 'UNITED KINGDOM OF GREAT BRITAIN AND NORTHERN IRELAND', 'UK'),
        (237, '255', 'TZ', 'Tanzanian Shilling', 'United Republic of Tanzania (the)', 'TZS', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania'),
        (240, '1', 'US', 'US Dollar', 'United States of America (the)', 'USD', 'UNITED STATES OF AMERICA', 'US'),
        (241, '598', 'UY', 'Peso Uruguayo', 'Uruguay', 'UYU', 'URUGUAY', 'Uruguay'),
        (242, '998', 'UZ', 'Uzbekistan Sum', 'Uzbekistan', 'UZS', 'UZBEKISTAN', 'Uzbekistan'),
        (243, '678', 'VU', 'Vatu', 'Vanuatu', 'VUV', 'VANUATU', 'Vanuatu'),
        (244, '58', 'VE', 'Bolívar', 'Venezuela (Bolivarian Republic of)', 'VES', 'VENEZUELA (BOLIVARIAN REPUBLIC OF)', 'Venezuela'),
        (245, '84', 'VN', 'Dong', 'Viet Nam', 'VND', 'VIET NAM', 'Vietnam'),
        (246, '681', 'WF', 'CFP Franc', '', 'XPF', 'WALLIS AND FUTUNA', 'Wallis & Futuna'),
        (247, '212', 'EH', 'Moroccan Dirham', '', 'MAD', 'WESTERN SAHARA', 'Western Sahara'),
        (248, '967', 'YE', 'Yemeni Rial', 'Yemen', 'YER', 'YEMEN', 'Yemen'),
        (249, '260', 'ZM', 'Zambian Kwacha', 'Zambia', 'ZMW', 'ZAMBIA', 'Zambia'),
        (250, '263', 'ZW', 'Zimbabwe Dollar', 'Zimbabwe', 'ZWL', 'ZIMBABWE', 'Zimbabwe'),
        (251, '358', 'AX', 'Euro', '', 'EUR', 'ÅLAND ISLANDS', 'Åland Islands');");
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
