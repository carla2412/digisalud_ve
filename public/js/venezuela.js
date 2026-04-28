const ubicaciones = {
  "Amazonas": {
    "Alto Orinoco": [
      "La Esmeralda",
      "Acanaña",
      "Toky Shamanaña",
      "Mavaka",
      "Parimabé"
    ],
    "Atabapo": [
      "San Fernando de Atabapo",
      "Laja Lisa",
      "Macuruco",
      "Guarinuma"
    ],
    "Atures": [
      "Puerto Ayacucho",
      "Limón de Parhueña",
      "Platanillal"
    ],
    "Autana": [
      "Samariapo",
      "Pendare",
      "Munduapo",
      "San Pedro del Orinoco",
      "Isla Ratón"
    ],
    "Manapiare": [
      "Cacurí",
      "Manami",
      "Marueta",
      "San Juan de Manapiare"
    ],
    "Maroa": [
      "Maroa",
      "Victorino",
      "Comunidad"
    ],
    "Río Negro": [
      "Curimacare",
      "Cocuy",
      "San Carlos de Río Negro",
      "Solano"
    ]
  },

  "Anzoátegui": {
    "Anaco": [
      "Anaco",
      "Buena Vista",
      "San Joaquín"
    ],
    "Aragua": [
      "Aragua de Barcelona",
      "Cachipo"
    ],
    "Bolívar": [
      "Bergantín",
      "Caigua",
      "El Carmen",
      "El Pilar",
      "Naricual",
      "San Cristóbal"
    ],
    "Bruzual": [
      "Clarines",
      "Guanape",
      "Sabana de Uchire"
    ],
    "Cajigal": [
      "Onoto",
      "San Pablo"
    ],
    "Carvajal": [
      "Valle de Guanape",
      "Santa Bárbara"
    ],
    "Diego Bautista Urbaneja": [
      "Lechería",
      "El Morro"
    ],
    "Freites": [
      "Cantaura",
      "Libertador",
      "Santa Rosa",
      "Urica"
    ],
    "Guanipa": [
      "San José de Guanipa"
    ],
    "Guanta": [
      "Guanta",
      "Chorrerón"
    ],
    "Independencia": [
      "Mamo",
      "Soledad"
    ],
    "Libertad": [
      "San Mateo",
      "El Carito",
      "Santa Inés",
      "La Romereña"
    ],
    "McGregor": [
      "El Chaparro",
      "Tomás Alfaro Calatrava"
    ],
    "Miranda": [
      "Atapirire",
      "Boca del Pao",
      "El Pao",
      "Pariaguán"
    ],
    "Monagas": [
      "Mapire",
      "Piar",
      "San Diego de Cabrutica",
      "Santa Clara",
      "Uverito",
      "Zuata"
    ],
    "Peñalver": [
      "Puerto Píritu",
      "San Miguel",
      "Sucre"
    ],
    "Píritu": [
      "Píritu",
      "San Francisco"
    ],
    "San Juan de Capistrano": [
      "Boca de Uchire",
      "Boca de Chávez"
    ],
    "Santa Ana": [
      "Santa Ana",
      "Pueblo Nuevo"
    ],
    "Simón Rodríguez": [
      "Edmundo Barrios",
      "Miguel Otero Silva"
    ],
    "Sotillo": [
      "Puerto La Cruz",
      "Pozuelos"
    ]
  },

  "Apure": {
    "Achaguas": [
      "Achaguas",
      "Apurito",
      "El Yagual",
      "Guachara",
      "Mucuritas",
      "Queseras del Medio"
    ],
    "Biruaca": [
      "Biruaca"
    ],
    "Muñoz": [
      "Bruzual",
      "Mantecal",
      "Quintero",
      "Rincón Hondo",
      "San Vicente"
    ],
    "Páez": [
      "Guasdualito",
      "Aramendi",
      "El Amparo",
      "San Camilo",
      "Urdaneta"
    ],
    "Pedro Camejo": [
      "San Juan de Payara",
      "Codazzi",
      "Cunaviche"
    ],
    "Rómulo Gallegos": [
      "Elorza",
      "La Trinidad"
    ],
    "San Fernando": [
      "San Fernando",
      "El Recreo",
      "Peñalver",
      "San Rafael de Atamaica"
    ]
  },

  "Aragua": {
    "Bolívar": [
      "San Mateo"
    ],
    "Camatagua": [
      "Camatagua",
      "Carmen de Cura"
    ],
    "Francisco Linares Alcántara": [
      "Santa Rita",
      "Francisco de Miranda",
      "Monseñor Feliciano González"
    ],
    "Girardot": [
      "Choroní",
      "Las Delicias",
      "Madre María de San José",
      "Joaquín Crespo",
      "Pedro José Ovalles",
      "José Casanova Godoy",
      "Andrés Eloy Blanco",
      "Los Tacarigua"
    ],
    "José Ángel Lamas": [
      "Santa Cruz"
    ],
    "José Félix Ribas": [
      "La Victoria",
      "Castor Nieves Ríos",
      "Las Guacamayas",
      "Pao de Zárate",
      "Zuata"
    ],
    "José Rafael Revenga": [
      "El Consejo"
    ],
    "Libertador": [
      "Palo Negro",
      "San Martín de Porres"
    ],
    "Mario Briceño Iragorry": [
      "El Limón",
      "Caña de Azúcar"
    ],
    "Ocumare de la Costa de Oro": [
      "Ocumare de la Costa"
    ],
    "San Casimiro": [
      "San Casimiro",
      "Ollas de Caramacate",
      "Valle Morín",
      "Güiripa"
    ],
    "San Sebastián": [
      "San Sebastián de los Reyes"
    ],
    "Santiago Mariño": [
      "Turmero",
      "Arévalo Cedeño",
      "Chuao",
      "Samán de Güere",
      "Alfredo Pacheco Miranda"
    ],
    "Santos Michelena": [
      "Las Tejerías",
      "Tiara"
    ],
    "Sucre": [
      "Cagua",
      "Bella Vista"
    ],
    "Tovar": [
      "Colonia Tovar"
    ],
    "Urdaneta": [
      "Barbacoas",
      "Las Peñitas",
      "San Francisco de Cara",
      "Taguay"
    ],
    "Zamora": [
      "Villa de Cura",
      "Magdaleno",
      "San Francisco de Asís",
      "Valles de Tucutunemo",
      "Augusto Mijares"
    ]
  },

  "Barinas": {
    "Alberto Arvelo Torrealba": [
      "Sabaneta",
      "Rodríguez Domínguez"
    ],
    "Andrés Eloy Blanco": [
      "El Cantón",
      "Santa Cruz de Guacas",
      "Puerto Vivas"
    ],
    "Antonio José de Sucre": [
      "Bum Bum",
      "Ticoporo",
      "Nicolás Pulido"
    ],
    "Arismendi": [
      "Arismendi",
      "Guadarrama",
      "La Unión",
      "San Antonio"
    ],
    "Barinas": [
      "Barinas",
      "Alfredo Arvelo Larriva",
      "Alto Barinas",
      "Corazón de Jesús",
      "El Carmen",
      "Dominga Ortiz de Páez",
      "Manuel Palacio Fajardo",
      "Juan Antonio Rodríguez Domínguez",
      "Rómulo Betancourt"
    ],
    "Bolívar": [
      "Barinitas",
      "Altamira de Cáceres",
      "Calderas"
    ],
    "Cruz Paredes": [
      "Barrancas",
      "El Socorro",
      "Masparrito"
    ],
    "Ezequiel Zamora": [
      "Santa Bárbara",
      "José Ignacio del Pumar",
      "Pedro Briceño Méndez",
      "Ramón Ignacio Méndez"
    ],
    "Obispos": [
      "Obispos",
      "Guasimitos",
      "El Real",
      "La Luz"
    ],
    "Pedraza": [
      "Ciudad Bolivia",
      "José Félix Ribas",
      "Páez",
      "Independencia"
    ],
    "Rojas": [
      "Libertad",
      "Dolores",
      "Santa Rosa",
      "Palacio Fajardo"
    ],
    "Sosa": [
      "Ciudad de Nutrias",
      "El Regalo",
      "Puerto de Nutrias",
      "Santa Catalina"
    ]
  },

 
  "Bolívar": {
    "Angostura del Orinoco": [
      "Catedral",
      "Agua Salada",
      "La Sabanita",
      "Vista Hermosa",
      "Marhuanta",
      "José Antonio Páez",
      "Orinoco",
      "Panapana",
      "Zea"
    ],
    "Caroní": [
      "Cachamay",
      "Chirica",
      "Dalla Costa",
      "Once de Abril",
      "Simón Bolívar",
      "Unare",
      "Universidad",
      "Vista al Sol",
      "Pozo Verde",
      "Yocoima"
    ],
    "Cedeño": [
      "Caicara del Orinoco",
      "Ascensión Enverme",
      "Altagracia",
      "La Urbana",
      "Pijiguaos",
      "Quiribana"
    ],
    "El Callao": [
      "El Callao"
    ],
    "Gran Sabana": [
      "Santa Elena de Uairén",
      "Ikabarú"
    ],
    "Heres": [
      "Catedral",
      "Zea",
      "Orinoco"
    ],
    "Piar": [
      "Upata",
      "Andrés Eloy Blanco",
      "Pedro Cova"
    ],
    "Angostura": [
      "Ciudad Piar",
      "San Francisco",
      "Barceloneta",
      "Santa Bárbara"
    ],
    "Roscio": [
      "Guasipati",
      "Salom"
    ],
    "Sifontes": [
      "Tumeremo",
      "Dalla Costa",
      "San Isidro"
    ],
    "Sucre": [
      "Moitaco",
      "Borvón",
      "Guarataro"
    ],
    "Padre Pedro Chien": [
      "El Palmar"
    ]
  },

  "Carabobo": {
    "Bejuma": [
      "Bejuma",
      "Canoabo",
      "Simón Bolívar"
    ],
    "Carlos Arvelo": [
      "Güigüe",
      "Belén",
      "Tacarigua"
    ],
    "Diego Ibarra": [
      "Mariara",
      "Aguas Calientes"
    ],
    "Guacara": [
      "Guacara",
      "Ciudad Alianza",
      "Yagua"
    ],
    "Juan José Mora": [
      "Morón",
      "Urama"
    ],
    "Libertador": [
      "Tocuyito",
      "Independencia"
    ],
    "Los Guayos": [
      "Los Guayos"
    ],
    "Miranda": [
      "Miranda"
    ],
    "Montalbán": [
      "Montalbán"
    ],
    "Naguanagua": [
      "Naguanagua"
    ],
    "Puerto Cabello": [
      "Bartolomé Salom",
      "Democracia",
      "Fraternidad",
      "Goaigoaza",
      "Juan José Flores",
      "Unión",
      "Borburata",
      "Patanemo"
    ],
    "San Diego": [
      "San Diego"
    ],
    "San Joaquín": [
      "San Joaquín"
    ],
    "Valencia": [
      "Candelaria",
      "Catedral",
      "El Socorro",
      "Miguel Peña",
      "Rafael Urdaneta",
      "San Blas",
      "San José",
      "Santa Rosa",
      "Negro Primero"
    ]
  },

  "Cojedes": {
    "Anzoátegui": [
      "Cojedes",
      "Juan de Mata Suárez"
    ],
    "Falcom": [
      "Tinaquillo"
    ],
    "Girardot": [
      "El Baúl",
      "Sucre"
    ],
    "Lima Blanco": [
      "Macapo",
      "La Aguadita"
    ],
    "Pao de San Juan Bautista": [
      "El Pao"
    ],
    "Ricaurte": [
      "Libertad",
      "Santa Cruz"
    ],
    "Rómulo Gallegos": [
      "Las Vegas"
    ],
    "San Carlos": [
      "San Carlos de Austria",
      "Juan Ángel Bravo",
      "Manuel Manrique"
    ],
    "Tinaco": [
      "General en Jefe José Laurencio Silva"
    ]
  },

  "Delta Amacuro": {
    "Antonio Díaz": [
      "Curiapo",
      "Almirante Luis Brión",
      "Padre Barral",
      "Aniceto Lugo",
      "Manuel Renaud",
      "Santos de Abelgas"
    ],
    "Casacoima": [
      "Sierra Imataca",
      "Juan Bautista Arismendi",
      "Manuel Piar",
      "Rómulo Gallegos"
    ],
    "Pedernales": [
      "Pedernales",
      "Luis Beltrán Prieto Figueroa"
    ],
    "Tucupita": [
      "San José",
      "José Vidal Marcano",
      "Juan Millán",
      "Leonardo Ruiz Pineda",
      "Mariscal Antonio José de Sucre",
      "Monseñor Argimiro García",
      "San Rafael",
      "Virgen del Valle"
    ]
  },

  "Distrito Capital": {
    "Libertador": [
      "23 de Enero",
      "Altagracia",
      "Antímano",
      "Candelaria",
      "Caricuao",
      "Catedral",
      "Coche",
      "El Junquito",
      "El Paraíso",
      "El Recreo",
      "El Valle",
      "La Pastora",
      "La Vega",
      "Macarao",
      "San Agustín",
      "San Bernardino",
      "San José",
      "San Juan",
      "San Pedro",
      "Santa Rosalía",
      "Santa Teresa",
      "Sucre (Catia)"
    ]
  },

  "Falcón": {
    "Acosta": [
      "San Juan de los Cayos",
      "Capadare",
      "La Pastora",
      "Libertador"
    ],
    "Bolívar": [
      "San Luis",
      "Aracua",
      "La Peña"
    ],
    "Buchivacoa": [
      "Capatárida",
      "Bariro",
      "Borojó",
      "Guajiro",
      "Seque",
      "Zazárida",
      "Valle de Eroa"
    ],
    "Carirubana": [
      "Santa Ana",
      "Carirubana",
      "Punta Cardón",
      "Norte"
    ],
    "Colina": [
      "La Vela de Coro",
      "Acurigua",
      "Guaibacoa",
      "Las Calderas",
      "Macoruca"
    ],
    "Dabajuro": [
      "Dabajuro"
    ],
    "Democracia": [
      "Pedregal",
      "Agua Clara",
      "Avaria",
      "Pedregal",
      "Piedra Grande",
      "Purureche"
    ],
    "Falcón": [
      "Pueblo Nuevo",
      "Adícora",
      "Baraived",
      "Buena Vista",
      "Jadacaquiva",
      "Moruy",
      "Adaure",
      "El Vínculo",
      "El Hato"
    ],
    "Federación": [
      "Churuguara",
      "Agua Larga",
      "Mapararí",
      "El Paují",
      "Independencia"
    ],
    "Jacura": [
      "Jacura",
      "Agua Linda",
      "Araurima"
    ],
    "Los Taques": [
      "Los Taques",
      "Judibana"
    ],
    "Mauroa": [
      "Mene de Mauroa",
      "San Félix",
      "Casigua"
    ],
    "Miranda": [
      "Santa Ana de Coro",
      "Guzmán Guillermo",
      "Mitare",
      "Río Seco",
      "Sabaneta",
      "San Antonio",
      "San Gabriel"
    ],
    "Monseñor Iturriza": [
      "Chichiriviche",
      "Boca de Tocuyo",
      "Tocuyo de la Costa"
    ],
    "Palmasola": [
      "Palmasola"
    ],
    "Petit": [
      "Cabure",
      "Colina",
      "Curimagua"
    ],
    "Píritu": [
      "Píritu",
      "San José de la Costa"
    ],
    "San Francisco": [
      "Mirimire"
    ],
    "Silva": [
      "Tucacas",
      "Boca de Aroa"
    ],
    "Sucre": [
      "La Cruz de Taratara",
      "Sucre"
    ],
    "Tocópero": [
      "Tocópero"
    ],
    "Unión": [
      "Santa Cruz de Bucaral",
      "El Charal",
      "Las Vegas del Tuy"
    ],
    "Urumaco": [
      "Urumaco",
      "Bruzual"
    ],
    "Zamora": [
      "Puerto Cumarebo",
      "La Ciénaga",
      "La Soledad",
      "Pueblo Cumarebo",
      "Zazárida"
    ]},

  "Guárico": {
    "Camaguán": [
      "Camaguán",
      "Puerto Miranda",
      "Uverito"
    ],
    "Chaguaramas": [
      "Chaguaramas"
    ],
    "El Socorro": [
      "El Socorro"
    ],
    "Francisco de Miranda": [
      "Calabozo",
      "El Calvario",
      "El Rastro",
      "Guardatinajas"
    ],
    "José Félix Ribas": [
      "Tucupido",
      "San Rafael de Laya"
    ],
    "José Tadeo Monagas": [
      "Altagracia de Orituco",
      "San Rafael de Orituco",
      "San Francisco de Javier de Lezama",
      "Paso Real de Macaira",
      "Carlos Soublette",
      "San Francisco de Orituco",
      "Libertad de Orituco"
    ],
    "Juan Germán Roscio": [
      "San Juan de los Morros",
      "Cantagallo",
      "Parapara"
    ],
    "Juan José Rondón": [
      "Valle de la Pascua",
      "Espino"
    ],
    "Julián Mellado": [
      "El Sombrero",
      "Sosa"
    ],
    "Las Mercedes": [
      "Las Mercedes",
      "Cabruta",
      "Santa Rita de Manapire"
    ],
    "Ortiz": [
      "Ortiz",
      "San Francisco de Tiznados",
      "San José de Tiznados",
      "San Lorenzo de Tiznados"
    ],
    "Pedro Zaraza": [
      "Zaraza",
      "San José de Unare"
    ],
    "San Gerónimo de Guayabal": [
      "Guayabal",
      "Cazorla"
    ],
    "San José de Guaribe": [
      "San José de Guaribe"
    ],
    "Santa María de Ipire": [
      "Santa María de Ipire",
      "Altamira"
    ]
  },

  "Lara": {
    "Andrés Eloy Blanco": [
      "Pio Tamayo",
      "Quebrada Honda de Guache",
      "Yacambú"
    ],
    "Crespo": [
      "Freitez",
      "José María Blanco"
    ],
    "Iribarren": [
      "Catedral",
      "Concepción",
      "El Cují",
      "Juan de Villegas",
      "Santa Rosa",
      "Tamaca",
      "Unión",
      "Aguedo Felipe Alvarado",
      "Buena Vista",
      "Juárez"
    ],
    "Jiménez": [
      "Juan Bautista Rodríguez",
      "Cuara",
      "Diego de Lozada",
      "Paraíso de San José",
      "San Miguel",
      "Tintorero",
      "José Bernardo Dorante",
      "Coronel Mariano Peraza"
    ],
    "Morán": [
      "Bolívar",
      "Anzoátegui",
      "Guarico",
      "Hilario Luna y Luna",
      "Humocaro Alto",
      "Humocaro Bajo",
      "La Concordia",
      "Villanueva"
    ],
    "Palavecino": [
      "Cabudare",
      "José Gregorio Bastidas",
      "Agua Viva"
    ],
    "Simón Planas": [
      "Sarare",
      "Buría",
      "Gustavo Vegas León"
    ],
    "Torres": [
      "Trinidad Samuel",
      "Antonio Díaz",
      "Camacaro",
      "Castañeda",
      "Chiquinquirá",
      "Espinoza de los Monteros",
      "Lara",
      "Las Mercedes",
      "Manuel Morillo",
      "Montaña de Verde",
      "Montes de Oca",
      "Torres",
      "Heriberto Arroyo",
      "Reyes Vargas",
      "Altagracia"
    ],
    "Urdaneta": [
      "Siquisique",
      "Moroturo",
      "San Miguel",
      "Xaguas"
    ]
  },

  "Mérida": {
    "Alberto Adriani": [
      "Presidente Betancourt",
      "Presidente Páez",
      "Presidente Rómulo Gallegos",
      "Gabriel Picón González",
      "Héctor Amable Mora",
      "José Nucete Sardi",
      "Pulido Méndez"
    ],
    "Andrés Bello": [
      "La Azulita"
    ],
    "Antonio Pinto Salinas": [
      "Santa Cruz de Mora",
      "Mesa Bolívar",
      "Mesa de las Palmas"
    ],
    "Aricagua": [
      "Aricagua",
      "San Antonio"
    ],
    "Arzobispo Chacón": [
      "Canaguá",
      "Capurí",
      "Chacantá",
      "El Molino",
      "Guaraque",
      "Mucutuy",
      "Mucuchachí"
    ],
    "Campo Elías": [
      "Fernández Peña",
      "Matriz",
      "Montalbán",
      "Acequias",
      "Jají",
      "La Mesa",
      "San José"
    ],
    "Caracciolo Parra Olmedo": [
      "Tucaní",
      "Florencio Ramírez"
    ],
    "Cardenal Quintero": [
      "Santo Domingo",
      "Las Piedras"
    ],
    "Guaraque": [
      "Guaraque",
      "Mesa de Quintero",
      "Río Negro"
    ],
    "Julio César Salas": [
      "Arapuey",
      "Palmira"
    ],
    "Justo Briceño": [
      "Torondoy",
      "San Cristóbal de Torondoy"
    ],
    "Libertador": [
      "Antonio Spinetti Dini",
      "Arias",
      "Caracciolo Parra Pérez",
      "Domingo Peña",
      "El Llano",
      "Gonzalo Picón Febres",
      "Jacinto Plaza",
      "Juan Rodríguez Suárez",
      "Lasso de la Vega",
      "Mariano Picón Salas",
      "Milla",
      "Osuna Rodríguez",
      "Sagrario",
      "Santa Elena",
      "Santuario"
    ],
    "Miranda": [
      "Timotes",
      "Andrés Eloy Blanco",
      "Piñango",
      "La Venta"
    ],
    "Obispo Ramos de Lora": [
      "Santa Elena de Arenales",
      "Eloy Paredes",
      "San Rafael de Alcázar"
    ],
    "Padre Noguera": [
      "Santa María de Caparo"
    ],
    "Pueblo Llano": [
      "Pueblo Llano"
    ],
    "Rangel": [
      "Mucuchíes",
      "Cacute",
      "La Toma",
      "Mucurubá",
      "San Rafael"
    ],
    "Rivas Dávila": [
      "Bailadores",
      "Gerónimo Maldonado"
    ],
    "Santos Marquina": [
      "Tabay"
    ],
    "Sucre": [
      "Lagunillas",
      "Chiguará",
      "Estánquez",
      "La Trampa",
      "Pueblo Nuevo del Sur",
      "San Juan"
    ],
    "Tovar": [
      "Tovar",
      "El Llano",
      "San Francisco",
      "El Amparo"
    ],
    "Tulio Febres Cordero": [
      "Nueva Bolivia",
      "Independencia",
      "María de la Concepción Palacios Blanco",
      "Santa Apolonia"
    ],
    "Zea": [
      "Zea",
      "Caño El Tigre"
    ]
  },
  "Monagas": {
    "Acosta": [
      "San Antonio de Maturín",
      "San Francisco de Maturín"
    ],
    "Aguasay": [
      "Aguasay"
    ],
    "Bolívar": [
      "Caripito"
    ],
    "Caripe": [
      "Caripe",
      "El Guácharo",
      "La Guanota",
      "Sabana de Piedra",
      "San Agustín",
      "Teresén"
    ],
    "Cedeño": [
      "Caicara de Maturín",
      "Areo",
      "Ascensión de Fermín",
      "Viento Fresco"
    ],
    "Ezequiel Zamora": [
      "Punta de Mata",
      "El Tejero"
    ],
    "Libertador": [
      "Temblador",
      "Chaguaramas",
      "Las Alhuacas",
      "Tabasca"
    ],
    "Maturín": [
      "Alto de los Godos",
      "Boquerón",
      "Las Cocuizas",
      "La Pica",
      "San Simón",
      "Santa Cruz",
      "San Vicente",
      "El Corozo",
      "El Furrial",
      "Jusepín",
      "La Pica"
    ],
    "Piar": [
      "Aragua de Maturín",
      "Aparicio",
      "Chaguaramal",
      "El Pinto",
      "Guanaguana",
      "La Toscana"
    ],
    "Punceres": [
      "Quiriquire",
      "Cachipo"
    ],
    "Santa Bárbara": [
      "Santa Bárbara de Tapirin"
    ],
    "Sotillo": [
      "Barrancas del Orinoco",
      "Los Barrancos de Fajardo"
    ],
    "Uracoa": [
      "Uracoa"
    ]
  },
  "Sucre": {
    "Andrés Eloy Blanco": [
      "Casanay",
      "Rómulo Gallegos"
    ],
    "Andrés Mata": [
      "San José de Aerocuar",
      "Tavera Acosta"
    ],
    "Arismendi": [
      "Río Caribe",
      "Antonio José de Sucre",
      "San Juan de las Galdonas",
      "Puerto Santo",
      "El Morro de Puerto Santo"
    ],
    "Benítez": [
      "El Pilar",
      "El Rincón",
      "General Francisco Antonio Vásquez",
      "Guaraúnos",
      "Tunapuicito",
      "Unión"
    ],
    "Bermúdez": [
      "Carúpano",
      "Santa Catalina",
      "Santa Rosa",
      "Bolívar",
      "Macarapana"
    ],
    "Bolívar": [
      "Marigüitar"
    ],
    "Cajigal": [
      "Yaguaraparo",
      "Libertad",
      "El Paujil"
    ],
    "Cruz Salmerón Acosta": [
      "Araya",
      "Chacopata",
      "Manicuare"
    ],
    "Libertador": [
      "Tunapuy",
      "Campo Elías"
    ],
    "Mariño": [
      "Irapa",
      "Campo Claro",
      "Marabal",
      "San Antonio de Irapa",
      "Soro"
    ],
    "Mejía": [
      "San Antonio del Golfo"
    ],
    "Montes": [
      "Cumanacoa",
      "Arenas",
      "Aricagua",
      "Cocollar",
      "San Fernando",
      "San Lorenzo"
    ],
    "Ribero": [
      "Cariaco",
      "Catuaro",
      "Rendón",
      "Santa Cruz",
      "Santa María"
    ],
    "Sucre": [
      "Cumaná",
      "Altagracia",
      "Santa Inés",
      "Valentín Valiente",
      "Ayacucho",
      "San Juan",
      "Raúl Leoni"
    ],
    "Valdez": [
      "Güiria",
      "Cristóbal Colón",
      "Bideau",
      "Punta de Piedras"
    ]
  },
  "Miranda": {
    "Buroz": ["Mamporal"],
    "Chacao": ["Chacao"],
    "Guaicaipuro": ["Altagracia de la Montaña","Cecilio Acosta","Los Teques","El Jarillo","San Pedro","Tácata","Paracotos"],
    "Plaza": ["Guarenas"],
    "Zamora": ["Guatire", "Araira"],
    "Carrizal": ["Carrizal"],
    "Cristóbal Rojas": ["Charallave", "Las Brisas"],
    "Independencia": ["Santa Teresa del Tuy", "el Cartanal"],
    "Lander": ["Ocumare del Tuy","La Democracia","Santa Bárbara","La Mata","La Cabrera"],
    "Acevedo": ["Aragüita","Arévalo González","Capaya","Caucagua","Panaquire","Ribas","El Café","Marizapa","Yaguapa"],
    "Páez": ["Río Chico","El Guapo","Tacarigua de la Laguna", "Paparo","San Fernando del Guapo"],
    "Andrés Bello": ["San José de Barlovento", "Cumbo"],
    "Brión": ["Higuerote","Curiepe","Tacarigua de Brión","Chirimena","Birongo"],
    "Sucre": ["Petare","Leoncio Martínez", "Caucagüita", "Filas de Mariche","La Dolorita"],
    "Baruta": ["El Cafetal", "Las Minas", "Nuestra Señora del Rosario"],
    "El Hatillo": ["El Hatillo"],
    "Los Salias": ["San Antonio de los Altos"],
    "Pedro Gual": ["Cúpira","Machurucuto","Guarabe"],
     "Urdaneta":[" Cúa", "Nueva Cúa"],
    "Simón Bolívar":["San Antonio de Yare", "San Francisco de Yare"],
     "Paz Castillo":["Santa Rita", "Santa Lucía del Tuy","Soapire","Siquire"]
    
  },
  "Táchira": {
    "Andrés Bello": [
      "Cordero"
    ],
    "Antonio Rómulo Costa": [
      "Las Mesas"
    ],
    "Ayacucho": [
      "San Juan de Colón",
      "San Pedro del Río",
      "Rivas Berti"
    ],
    "Bolívar": [
      "San Antonio del Táchira",
      "Juan Vicente Gómez",
      "Isaías Medina Angarita",
      "Palotal"
    ],
    "Cárdenas": [
      "Tariiba",
      "Amenodoro Rangel Lamús",
      "La Florida"
    ],
    "Córdoba": [
      "Santa Ana del Táchira"
    ],
    "Fernández Feo": [
      "San Rafael del Piñal",
      "Alberto Adriani",
      "Santo Domingo"
    ],
    "Francisco de Miranda": [
      "San José de Bolívar"
    ],
    "García de Hevia": [
      "La Fría",
      "Boca de Grita",
      "José Antonio Páez"
    ],
    "Guásimos": [
      "Palmira"
    ],
    "Independencia": [
      "Capacho Nuevo",
      "Juan Germán Roscio",
      "Roman Cárdenas"
    ],
    "Jáuregui": [
      "La Grita",
      "Emilio Constantino Guerrero",
      "Monseñor Miguel Antonio Salas"
    ],
    "José María Vargas": [
      "El Cobre"
    ],
    "Junín": [
      "Rubio",
      "Bramón",
      "La Petrólea",
      "Quinimarí"
    ],
    "Libertad": [
      "Capacho Viejo",
      "Cipriano Castro",
      "Manuel Felipe Rugeles"
    ],
    "Libertador": [
      "Abejales",
      "Emeterio Ochoa",
      "Doradas",
      "San Joaquín de Navay"
    ],
    "Lobatera": [
      "Lobatera",
      "Constitución"
    ],
    "Michelena": [
      "Michelena"
    ],
    "Panamericano": [
      "Coloncito",
      "La Palmita"
    ],
    "Pedro María Ureña": [
      "Ureña",
      "Nueva Arcadia"
    ],
    "Rafael Urdaneta": [
      "Delicias"
    ],
    "Samuel Darío Maldonado": [
      "La Tendida",
      "Boconó",
      "Hernández"
    ],
    "San Cristóbal": [
      "La Concordia",
      "San Juan Bautista",
      "Pedro María Morantes",
      "San Sebastián",
      "Dr. Francisco Romero Lobo"
    ],
    "San Judas Tadeo": [
      "Umuquena"
    ],
    "Seboruco": [
      "Seboruco"
    ],
    "Simón Rodríguez": [
      "San Simón"
    ],
    "Sucre": [
      "Queniquea",
      "Eleazar López Contreras",
      "San Pablo"
    ],
    "Torbes": [
      "San Josecito"
    ],
    "Uribante": [
      "Pregonero",
      "Cárdenas",
      "Potosí",
      "Juan Pablo Peñalosa"
    ]
  },
  "Portuguesa": {
    "Agua Blanca": [
      "Agua Blanca"
    ],
    "Araure": [
      "Araure",
      "Río Acarigua"
    ],
    "Esteller": [
      "Píritu",
      "Uveral"
    ],
    "Guanare": [
      "Guanare",
      "Córdoba",
      "San José de la Montaña",
      "San Juan de Guanaguanare",
      "Virgen de Coromoto"
    ],
    "Guanarito": [
      "Guanarito",
      "Trinidad de la Capilla",
      "Divina Pastora"
    ],
    "Monseñor Iturriza": [
      "Chichiriviche",
      "Boca de Tocuyo",
      "Tocuyo de la Costa"
    ],
    "Ospino": [
      "Ospino",
      "Aparición",
      "La Estación"
    ],
    "Páez": [
      "Acarigua",
      "Payara",
      "Pimpinela",
      "Ramón Peraza"
    ],
    "Papelón": [
      "Papelón",
      "Caño Delgadito"
    ],
    "San Genaro de Boconoíto": [
      "Boconoíto",
      "Antolín Tovar"
    ],
    "San Rafael de Onoto": [
      "San Rafael de Onoto",
      "Santa Fe",
      "Thermo Morales"
    ],
    "Santa Rosalía": [
      "El Playón",
      "Florida"
    ],
    "Sucre": [
      "Biscucuy",
      "Concepción",
      "San Rafael de Palo Alzado",
      "Uvencio Antonio Velásquez",
      "San José de Saguaz",
      "Villa Rosa"
    ],
    "Turén": [
      "Villa Bruzual",
      "Canelones",
      "Santa Cruz",
      "San Isidro Labrador"
    ],
    "Unda": [
      "Chabasquén",
      "Peña Blanca"
    ]
  },
 "Nueva Esparta": {
    "Antolín del Campo": [
      "Plaza de Paraguachí"
    ],
    "Arismendi": [
      "La Asunción"
    ],
    "Díaz": [
      "San Juan Bautista",
      "Zabala"
    ],
    "García": [
      "Valle del Espíritu Santo",
      "Francisco Fajardo"
    ],
    "Gómez": [
      "Santa Ana",
      "Guevara",
      "Matasiete",
      "Bolívar",
      "Sucre"
    ],
    "Maneiro": [
      "Pampatar",
      "Aguirre"
    ],
    "Marcano": [
      "Juan Griego",
      "Adrián"
    ],
    "Mariño": [
      "Porlamar"
    ],
    "Península de Macanao": [
      "Boca de Río",
      "San Francisco"
    ],
    "Tubores": [
      "Punta de Piedras",
      "Los Baleales"
    ],
    "Villalba": [
      "San Pedro de Coche",
      "Vicente Fuentes"
    ]
  },
  "Zulia": {
    "Almirante Padilla": [
      "Isla de Toas",
      "Monagas"
    ],
    "Baralt": [
      "San Timoteo",
      "General Bernardino Caballero",
      "Libertad",
      "Marcelino Briceño",
      "Nuevo Moroturo",
      "Pueblo Nuevo",
      "Manuel Guanipa Matos"
    ],
    "Cabimas": [
      "Ambrosio",
      "Carmen Herrera",
      "La Rosa",
      "Germán Ríos Linares",
      "San Benito",
      "Rómulo Betancourt",
      "Jorge Hernández",
      "Arístides Calvani",
      "Punta Gorda"
    ],
    "Catatumbo": [
      "Encontrados",
      "Udón Pérez"
    ],
    "Colón": [
      "Moralito",
      "San Carlos del Zulia",
      "Santa Cruz del Zulia",
      "Santa Bárbara",
      "Urribarrí"
    ],
    "Francisco Javier Pulgar": [
      "Simón Rodríguez",
      "Carlos Quevedo",
      "Francisco Javier Pulgar",
      "Agustín Codazzi"
    ],
    "Guajira": [
      "Alta Guajira",
      "Elias Sánchez Rubio",
      "Guajira",
      "Sinamaica"
    ],
    "Jesús Enrique Lossada": [
      "La Concepción",
      "San José",
      "Mariano Parra León",
      "José Ramón Yépez"
    ],
    "Jesús María Semprún": [
      "Jesús María Semprún",
      "Barí"
    ],
    "La Cañada de Urdaneta": [
      "Concepción",
      "Andrés Bello",
      "Chiquinquirá",
      "El Carmelo",
      "Potreritos"
    ],
    "Lagunillas": [
      "Alonso de Ojeda",
      "Libertad",
      "Campo Lara",
      "Eleazar López Contreras",
      "Venezuela"
    ],
    "Machiques de Perijá": [
      "Libertad",
      "Bartolomé de las Casas",
      "Río Negro",
      "San José de Perijá"
    ],
    "Mara": [
      "San Rafael",
      "La Sierrita",
      "Las Parcelas",
      "Luis de Vicente",
      "Monseñor Marcos Sergio Godoy",
      "Ricaurte",
      "Tamare"
    ],
    "Maracaibo": [
      "Antonio Borjas Romero",
      "Bolívar",
      "Cacique Mara",
      "Caracciolo Parra Pérez",
      "Cecilio Acosta",
      "Cristo de Aranza",
      "Coquivacoa",
      "Chiquinquirá",
      "Francisco Eugenio Bustamante",
      "Idelfonso Vásquez",
      "Juana de Ávila",
      "Luis Hurtado Higuera",
      "Manuel Dagnino",
      "Olegario Villalobos",
      "Raúl Leoni",
      "Santa Lucía",
      "Venancio Pulgar",
      "San Isidro"
    ],
    "Miranda": [
      "Altagracia",
      "Faria",
      "Ana María Campos",
      "San Antonio",
      "San José"
    ],
    "Independencia": [
      "Palmarejo"
    ],
    "Páez": [
      "Sinamaica",
      "Alta Guajira",
      "Elias Sánchez Rubio",
      "Guajira"
    ],
    "Rosario de Perijá": [
      "La Villa del Rosario",
      "Donaldo García",
      "Sixto Zambrano"
    ],
    "San Francisco": [
      "San Francisco",
      "El Bajo",
      "Domitila Flores",
      "Francisco Ochoa",
      "Los Cortijos",
      "Marcial Hernández",
      "José Domingo Rus"
    ],
    "Santa Rita": [
      "Santa Rita",
      "El Mene",
      "Pedro Lucas Urribarrí",
      "José Cenobio Urribarrí"
    ],
    "Simón Bolívar": [
      "Rafael Maria Baralt",
      "Manuel Manrique",
      "Rafael Urdaneta"
    ],
    "Sucre": [
      "Bobures",
      "Gibraltar",
      "Heras",
      "Monseñor Arturo Celeste Álvarez",
      "Rómulo Gallegos",
      "El Batey"
    ],
    "Valmore Rodríguez": [
      "La Victoria",
      "Rafael Urdaneta",
      "Raúl Cuenca"
    ]
  }
};
