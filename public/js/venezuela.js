const ubicaciones = {
  "Amazonas": {
    "Atures": ["Puerto Ayacucho", "Samariapo"],
    "Atabapo": ["San Fernando de Atabapo"],
    "Río Negro": ["San Carlos de Río Negro", "Solano"],
    "Maroa": ["Maroa"],
    "Manapiare": ["San Juan de Manapiare"],
    "Autana": ["Isla Ratón", "Santa Bárbara del Orinoco"],
    "Alto Orinoco": ["La Esmeralda", "Victorino"]
  },

  "Anzoátegui": {
    "Anaco": ["Anaco", "San Joaquín de Anaco"],
    "Simón Bolívar": ["Barcelona"],
    "Juan Antonio Sotillo": ["Puerto La Cruz"],
    "Simón Rodríguez": ["El Tigre"],
    "Diego Bautista Urbaneja": ["Lechería"],
    "Freites": ["Cantaura", "Urica"],
    "San José de Guanipa": ["El Tigrito"],
    "Miranda": ["Pariaguán"],
    "Aragua": ["Aragua de Barcelona"],
    "McGregor": ["Onoto"]
  },

  "Apure": {
    "San Fernando": ["San Fernando de Apure"],
    "Páez": ["Guasdualito"],
    "Rómulo Gallegos": ["Elorza"],
    "Pedro Camejo": ["San Juan de Payara"],
    "Biruaca": ["Biruaca"],
    "Muñoz": ["Bruzual", "Mantecal"],
    "Achaguas": ["Achaguas"],
    "Autonomía": ["Guachara"]
  },

  "Aragua": {
    "Girardot": ["Maracay", "El Limón"],
    "Ribas": ["La Victoria"],
    "Santiago Mariño": ["Turmero"],
    "Sucre": ["Cagua"],
    "Bolívar": ["San Mateo"],
    "Libertador": ["Palo Negro"],
    "Zamora": ["Villa de Cura"],
    "José Ángel Lamas": ["Santa Cruz"],
    "Santos Michelena": ["Las Tejerías"],
    "Tovar": ["Colonia Tovar"]
  },

  "Barinas": {
    "Barinas": ["Barinas"],
    "Antonio José de Sucre": ["Socopó"],
    "Pedraza": ["Ciudad Bolivia"],
    "Ezequiel Zamora": ["Santa Bárbara de Barinas"],
    "Obispos": ["Obispos"],
    "Andrés Eloy Blanco": ["Barrancas"],
    "Bolívar": ["Barinitas"],
    "Cruz Paredes": ["Barrancas"],
    "Sosa": ["Ciudad de Nutrias"],
    "Alberto Arvelo Torrealba": ["Sabaneta"]
  },

  "Bolívar": {
    "Heres": ["Ciudad Bolívar"],
    "Caroní": ["Ciudad Guayana", "Puerto Ordaz", "San Félix"],
    "Piar": ["Upata"],
    "El Callao": ["El Callao"],
    "Sifontes": ["Tumeremo"],
    "Roscio": ["Guasipati"],
    "Sucre": ["Maripa"],
    "Gran Sabana": ["Santa Elena de Uairén"],
    "Cedeño": ["Caicara del Orinoco"],
    "Padre Pedro Chien": ["El Palmar"]
  },

  "Carabobo": {
    "Valencia": ["Valencia"],
    "Naguanagua": ["Naguanagua"],
    "Guacara": ["Guacara"],
    "San Joaquín": ["San Joaquín"],
    "Bejuma": ["Bejuma"],
    "Montalbán": ["Montalbán"],
    "Diego Ibarra": ["Mariara"],
    "Juan José Mora": ["Morón"],
    "Libertador": ["Tocuyito"],
    "Puerto Cabello": ["Puerto Cabello"]
  },

  "Cojedes": {
    "Ezequiel Zamora": ["San Carlos"],
    "Tinaquillo": ["Tinaquillo"],
    "Girardot": ["El Baúl"],
    "Ricaurte": ["Libertad"],
    "Tinaco": ["Tinaco"],
    "Anzoátegui": ["Cojedes"],
    "Pao de San Juan Bautista": ["El Pao"],
    "Lima Blanco": ["Macapo"],
    "Rómulo Gallegos": ["Las Vegas"]
  },

  "Delta Amacuro": {
    "Tucupita": ["Tucupita"],
    "Antonio Díaz": ["Curiapo", "Araguaimujo"],
    "Pedernales": ["Pedernales"],
    "Casacoima": ["Sierra Imataca"]
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
    "Miranda": ["Coro"],
    "Carirubana": ["Punto Fijo"],
    "Monseñor Iturriza": ["Tucacas"],
    "Federación": ["Churuguara"],
    "Buchivacoa": ["Capatárida"],
    "Zamora": ["Puerto Cumarebo"],
    "Colina": ["La Vela de Coro"],
    "Acosta": ["San Juan de los Cayos"],
    "Mauroa": ["Mene de Mauroa"],
    "Los Taques": ["Santa Cruz de Los Taques"]
  },

  "Guárico": {
    "Juan Germán Roscio": ["San Juan de los Morros"],
    "Miranda": ["Calabozo"],
    "Zaraza": ["Zaraza"],
    "Leonardo Infante": ["Valle de la Pascua"],
    "Monagas": ["Altagracia de Orituco"],
    "Ortiz": ["Ortiz"],
    "Camaguán": ["Camaguán"],
    "José Félix Ribas": ["Tucupido"],
    "El Socorro": ["El Socorro"],
    "San José de Guaribe": ["San José de Guaribe"]
  },

  "Lara": {
    "Iribarren": ["Barquisimeto"],
    "Palavecino": ["Cabudare"],
    "Torres": ["Carora"],
    "Jiménez": ["Quíbor"],
    "Moran": ["El Tocuyo"],
    "Crespo": ["Duaca"],
    "Andrés Eloy Blanco": ["Sanare"],
    "Simón Planas": ["Sarare"],
    "Urdaneta": ["Siquisique"]
  },

  "Mérida": {
    "Libertador": ["Mérida"],
    "Campo Elías": ["Ejido"],
    "Alberto Adriani": ["El Vigía"],
    "Tovar": ["Tovar"],
    "Rivas Dávila": ["Bailadores"],
    "Rangel": ["Mucuchíes"],
    "Antonio Pinto Salinas": ["Santa Cruz de Mora"],
    "Miranda": ["Timotes"],
    "Zea": ["Zea"],
    "Santos Marquina": ["Tabay"]
  },

  "Miranda": {
    "Guaicaipuro": ["Los Teques"],
    "Plaza": ["Guarenas"],
    "Zamora": ["Guatire"],
    "Carrizal": ["Carrizal"],
    "Cristóbal Rojas": ["Charallave"],
    "Independencia": ["Santa Teresa del Tuy"],
    "Lander": ["Ocumare del Tuy"],
    "Acevedo": ["Caucagua"],
    "Páez": ["Río Chico"],
    "Andrés Bello": ["San José de Barlovento"],
    "Brión": ["Higuerote"],
    "Sucre": ["Petare"],
    "Baruta": ["Baruta"],
    "El Hatillo": ["El Hatillo"],
    "Los Salias": ["San Antonio de los Altos"],
    "Chacao": ["Chacao"]
  },

  "Zulia": {
    "Maracaibo": ["Maracaibo"],
    "Cabimas": ["Cabimas"],
    "San Francisco": ["San Francisco"],
    "Lagunillas": ["Ciudad Ojeda"],
    "Machiques de Perijá": ["Machiques"],
    "Colón": ["Santa Bárbara del Zulia"],
    "Jesús Enrique Lossada": ["La Concepción"],
    "Baralt": ["Mene Grande"],
    "Catatumbo": ["Encontrados"],
    "Guajira": ["Paraguaipoa"]
  }
};
