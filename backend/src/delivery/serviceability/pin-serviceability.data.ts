/**
 * Centralized PIN & District Serviceability Dataset for ShishaRent
 *
 * CRITICAL BUSINESS RULE:
 * ShishaRent delivers strictly and exclusively within 3 districts:
 * 1. Kolkata
 * 2. North 24 Parganas
 * 3. South 24 Parganas
 *
 * All other districts in West Bengal and all other states in India are NOT SERVICEABLE.
 */

export const ALLOWED_DELIVERY_DISTRICTS = [
  'Kolkata',
  'North 24 Parganas',
  'South 24 Parganas',
] as const;

export type AllowedDeliveryDistrict = (typeof ALLOWED_DELIVERY_DISTRICTS)[number];

export interface PinDirectoryEntry {
  pin: string;
  district: string;
  state: string;
  area: string;
  isServiceable: boolean;
}

/**
 * Comprehensive Postal Directory mapping PINs to exact District and State.
 */
export const PIN_DIRECTORY: Record<string, Omit<PinDirectoryEntry, 'pin' | 'isServiceable'>> = {
  // =========================================================================
  // 1. KOLKATA DISTRICT (Serviceable)
  // =========================================================================
  '700001': { district: 'Kolkata', state: 'West Bengal', area: 'Kolkata G.P.O. / Dalhousie' },
  '700002': { district: 'Kolkata', state: 'West Bengal', area: 'Cossipore' },
  '700003': { district: 'Kolkata', state: 'West Bengal', area: 'Baghbazar' },
  '700004': { district: 'Kolkata', state: 'West Bengal', area: 'Shyambazar' },
  '700005': { district: 'Kolkata', state: 'West Bengal', area: 'Hatkhola / Shobhabazar' },
  '700006': { district: 'Kolkata', state: 'West Bengal', area: 'Beadon Street' },
  '700007': { district: 'Kolkata', state: 'West Bengal', area: 'Barabazar' },
  '700008': { district: 'Kolkata', state: 'West Bengal', area: 'Barisha / Diamond Harbour Rd' },
  '700009': { district: 'Kolkata', state: 'West Bengal', area: 'Amherst Street / College St' },
  '700010': { district: 'Kolkata', state: 'West Bengal', area: 'Beliaghata' },
  '700011': { district: 'Kolkata', state: 'West Bengal', area: 'Narkeldanga' },
  '700012': { district: 'Kolkata', state: 'West Bengal', area: 'Bowbazar / Central' },
  '700013': { district: 'Kolkata', state: 'West Bengal', area: 'Dharmatala / Esplanade' },
  '700014': { district: 'Kolkata', state: 'West Bengal', area: 'Entally / CIT Road' },
  '700015': { district: 'Kolkata', state: 'West Bengal', area: 'Tangra / Topsia' },
  '700016': { district: 'Kolkata', state: 'West Bengal', area: 'Park Street / Camac Street / Russell St' },
  '700017': { district: 'Kolkata', state: 'West Bengal', area: 'Circus Avenue / Theatre Road' },
  '700018': { district: 'Kolkata', state: 'West Bengal', area: 'Garden Reach' },
  '700019': { district: 'Kolkata', state: 'West Bengal', area: 'Ballygunge / Park Circus / Chamru Khansama Ln' },
  '700020': { district: 'Kolkata', state: 'West Bengal', area: 'Elgin Road / Bhowanipore' },
  '700021': { district: 'Kolkata', state: 'West Bengal', area: 'Fort William / Maidan' },
  '700022': { district: 'Kolkata', state: 'West Bengal', area: 'Hastings' },
  '700023': { district: 'Kolkata', state: 'West Bengal', area: 'Kidderpore' },
  '700024': { district: 'Kolkata', state: 'West Bengal', area: 'Metiabruz / Garden Reach' },
  '700025': { district: 'Kolkata', state: 'West Bengal', area: 'Kalighat / Hazra' },
  '700026': { district: 'Kolkata', state: 'West Bengal', area: 'Rash Behari Avenue / Southern Avenue' },
  '700029': { district: 'Kolkata', state: 'West Bengal', area: 'Sarat Bose Road' },
  '700031': { district: 'Kolkata', state: 'West Bengal', area: 'Dhakuria / Selimpur' },
  '700033': { district: 'Kolkata', state: 'West Bengal', area: 'Regent Park / Golf Club' },
  '700037': { district: 'Kolkata', state: 'West Bengal', area: 'Belgachia' },
  '700039': { district: 'Kolkata', state: 'West Bengal', area: 'Tiljala / Topsia Road' },
  '700040': { district: 'Kolkata', state: 'West Bengal', area: 'Tollygunge / Kudghat' },
  '700045': { district: 'Kolkata', state: 'West Bengal', area: 'Lake Gardens' },
  '700046': { district: 'Kolkata', state: 'West Bengal', area: 'Gobinda Khatick Rd / Tangra' },
  '700047': { district: 'Kolkata', state: 'West Bengal', area: 'Naktala / Garia' },
  '700054': { district: 'Kolkata', state: 'West Bengal', area: 'Kankurgachi / Phoolbagan' },
  '700062': { district: 'Kolkata', state: 'West Bengal', area: 'Golf Green' },
  '700067': { district: 'Kolkata', state: 'West Bengal', area: 'Ultadanga' },
  '700068': { district: 'Kolkata', state: 'West Bengal', area: 'Purba Putiary' },
  '700069': { district: 'Kolkata', state: 'West Bengal', area: 'Esplanade / Raj Bhavan' },
  '700071': { district: 'Kolkata', state: 'West Bengal', area: 'Park Street / Camac Street Area' },
  '700072': { district: 'Kolkata', state: 'West Bengal', area: 'Princep Street / Chandni Chowk' },
  '700073': { district: 'Kolkata', state: 'West Bengal', area: 'Chittaranjan Avenue / College Square' },
  '700076': { district: 'Kolkata', state: 'West Bengal', area: 'Regent Estate' },
  '700077': { district: 'Kolkata', state: 'West Bengal', area: 'Bansdroni' },
  '700082': { district: 'Kolkata', state: 'West Bengal', area: 'Haridevpur' },
  '700085': { district: 'Kolkata', state: 'West Bengal', area: 'Beliaghata Housing' },
  '700087': { district: 'Kolkata', state: 'West Bengal', area: 'New Market / Lindsay St' },
  '700092': { district: 'Kolkata', state: 'West Bengal', area: 'Jadavpur University' },
  '700095': { district: 'Kolkata', state: 'West Bengal', area: 'Prince Anwar Shah Road / South City' },
  '700105': { district: 'Kolkata', state: 'West Bengal', area: 'Topsia / Science City / EM Bypass' },

  // =========================================================================
  // 2. NORTH 24 PARGANAS DISTRICT (Serviceable)
  // =========================================================================
  '700028': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Dum Dum / Airport' },
  '700030': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Ghughudanga / Dum Dum' },
  '700035': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Baranagar / Alambazar' },
  '700036': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Sinthee / Cossipore Rd' },
  '700048': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Lake Town / Bangur Avenue' },
  '700049': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Nimta / Belgharia' },
  '700050': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Belgharia' },
  '700051': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Birati' },
  '700052': { district: 'North 24 Parganas', state: 'West Bengal', area: 'NSCBI Airport Kolkata' },
  '700055': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Dum Dum Cantonment' },
  '700056': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Kamarhati / Belgharia' },
  '700057': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Ariadaha / Dakshineswar' },
  '700058': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Dakshineswar' },
  '700059': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Deshbandhu Nagar / Baguiati' },
  '700064': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Salt Lake Sector I / Bidhannagar' },
  '700065': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Dum Dum Park / Bangur' },
  '700074': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Motijheel / Dum Dum' },
  '700079': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Italgacha / Airport' },
  '700080': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Mall Road / Dum Dum' },
  '700081': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Hatiara / New Town' },
  '700083': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Rajarhat Gopalpur' },
  '700089': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Lake Town / VIP Road' },
  '700090': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Sodepur' },
  '700091': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Salt Lake Sector V / Technopolis' },
  '700097': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Salt Lake Sector III / Broadway' },
  '700098': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Salt Lake Sector II / Stadium' },
  '700101': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Teghoria / VIP Road' },
  '700102': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Baguiati / Kestopur' },
  '700106': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Salt Lake Sector IV / Sukantanagar' },
  '700108': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Bonhooghly / Dunlop' },
  '700109': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Sodepur Station Rd' },
  '700110': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Agarpara' },
  '700111': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Ghola / Panihati' },
  '700112': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Sukchar' },
  '700113': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Khardah' },
  '700114': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Rahara' },
  '700115': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Titagarh' },
  '700116': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Barrackpore Court / Cantonment' },
  '700117': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Sreepally / Barrackpore' },
  '700118': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Palta' },
  '700119': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Ichapore' },
  '700120': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Nawabganj / Ichapore' },
  '700121': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Shyamnagar' },
  '700122': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Kankinara' },
  '700123': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Bhatpara' },
  '700124': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Barasat' },
  '700125': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Barasat Colony' },
  '700126': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Hridaypur / Barasat' },
  '700127': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Nabapally / Barasat' },
  '700128': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Kazipara / Barasat' },
  '700129': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Madhyamgram' },
  '700130': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Madhyamgram Bazar' },
  '700131': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Michael Nagar' },
  '700132': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Badu / Madhyamgram' },
  '700133': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Doltala / Madhyamgram' },
  '700134': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Barrackpore' },
  '700135': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Rajarhat / Action Area II' },
  '700136': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Rajarhat Chowmatha / Action Area III' },
  '700156': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area I / Eco Space' },
  '700157': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area II / Financial Hub' },
  '700158': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area I / DLF 1' },
  '700159': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area II / City Centre 2' },
  '700160': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area III / Unitech / Candor' },
  '700161': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area III / Shapoorji' },
  '700162': { district: 'North 24 Parganas', state: 'West Bengal', area: 'New Town Action Area III / Sukhobrishti' },
  '743122': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Bijpur / Halisahar' },
  '743125': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Bhatpara / Jagatdal' },
  '743126': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Garulia' },
  '743144': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Noapara' },
  '743145': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Kanchrapara' },
  '743165': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Naihati' },
  '743166': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Haznagar' },
  '743221': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Deganga' },
  '743232': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Gaighata' },
  '743234': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Gobardanga' },
  '743235': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Bongaon' },
  '743245': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Maslandapur' },
  '743248': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Ashoknagar' },
  '743263': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Habra' },
  '743401': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Haroa' },
  '743411': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Basirhat' },
  '743412': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Baduria' },
  '743422': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Hasnabad' },
  '743424': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Taki' },
  '743427': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Minakhan' },
  '743456': { district: 'North 24 Parganas', state: 'West Bengal', area: 'Hingalganj' },

  // =========================================================================
  // 3. SOUTH 24 PARGANAS DISTRICT (Serviceable)
  // =========================================================================
  '700027': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Alipore / National Library' },
  '700032': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Jadavpur / Sulekha' },
  '700034': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Behala' },
  '700038': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Sahapur / New Alipore' },
  '700041': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Paschim Putiary / Kudghat' },
  '700042': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kasba / Bosepukur' },
  '700043': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Batanagar / Maheshtala' },
  '700044': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Badartala' },
  '700053': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Taratala / New Alipore' },
  '700060': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kankhulia / Thakurpukur' },
  '700061': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Sarsuna / Behala' },
  '700063': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Thakurpukur' },
  '700070': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Barisha / Joka' },
  '700075': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Santoshpur / Ajoy Nagar' },
  '700078': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Haltu / Kasba' },
  '700084': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Garia / Baishnabghata' },
  '700086': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Garfa / Jadavpur' },
  '700088': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Behala Chowrasta' },
  '700093': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Baghajatin / Garia' },
  '700094': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Mukundapur / Peerless' },
  '700096': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Garia Station / Patuli' },
  '700099': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kalikapur / EM Bypass' },
  '700100': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Panchasayar / Peerless Hospital' },
  '700103': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Narendrapur / Sonarpur' },
  '700104': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Thakurpukur 3A Bus Stand' },
  '700107': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kasba / Anandapur / Ruby Hospital' },
  '700137': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Budge Budge' },
  '700138': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Pujali / Budge Budge' },
  '700139': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Birlapur' },
  '700140': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Nodakhali' },
  '700141': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Maheshtala' },
  '700142': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Rabindra Nagar / Santoshpur' },
  '700143': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Sarkarpool' },
  '700144': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Joka / IIM Calcutta' },
  '700145': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Rasapunja' },
  '700146': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Amtala' },
  '700147': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kanganberia' },
  '700148': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Shirakole' },
  '700149': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Bishnupur' },
  '700150': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Sonarpur / Rajpur' },
  '700151': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Harinavi / Rajpur' },
  '700152': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Mahamayatala / Garia' },
  '700153': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Boral / Sonarpur' },
  '700154': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Subhasgram' },
  '700155': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Champahati' },
  '743302': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Baruipur' },
  '743312': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Bhangar' },
  '743318': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Bishnupur South' },
  '743329': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Canning' },
  '743331': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Diamond Harbour' },
  '743337': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Joynagar' },
  '743347': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kakdwip' },
  '743355': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Kultali' },
  '743372': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Namkhana' },
  '743387': { district: 'South 24 Parganas', state: 'West Bengal', area: 'Baruipur Station Road' },

  // =========================================================================
  // 4. OTHER WEST BENGAL DISTRICTS (UNSUPPORTED / REJECTED)
  // =========================================================================
  // Howrah District (Rejected)
  '711101': { district: 'Howrah', state: 'West Bengal', area: 'Howrah Head Post Office' },
  '711102': { district: 'Howrah', state: 'West Bengal', area: 'Shibpur' },
  '711103': { district: 'Howrah', state: 'West Bengal', area: 'Kadamtala' },
  '711104': { district: 'Howrah', state: 'West Bengal', area: 'Salkia' },
  '711105': { district: 'Howrah', state: 'West Bengal', area: 'Liluah' },
  '711106': { district: 'Howrah', state: 'West Bengal', area: 'Bally' },
  '711109': { district: 'Howrah', state: 'West Bengal', area: 'Santragachi' },
  '711201': { district: 'Howrah', state: 'West Bengal', area: 'Belur Math' },
  '711202': { district: 'Howrah', state: 'West Bengal', area: 'Bally Durgapur' },
  '711204': { district: 'Howrah', state: 'West Bengal', area: 'Uttarpara' },
  '711302': { district: 'Howrah', state: 'West Bengal', area: 'Andul' },
  '711401': { district: 'Howrah', state: 'West Bengal', area: 'Uluberia' },

  // Hooghly District (Rejected)
  '712101': { district: 'Hooghly', state: 'West Bengal', area: 'Chinsurah' },
  '712122': { district: 'Hooghly', state: 'West Bengal', area: 'Bandel' },
  '712136': { district: 'Hooghly', state: 'West Bengal', area: 'Chandannagar' },
  '712201': { district: 'Hooghly', state: 'West Bengal', area: 'Rishra' },
  '712232': { district: 'Hooghly', state: 'West Bengal', area: 'Serampore' },
  '712246': { district: 'Hooghly', state: 'West Bengal', area: 'Hindmotor / Konnagar' },
  '712258': { district: 'Hooghly', state: 'West Bengal', area: 'Dankuni' },

  // Nadia District (Rejected)
  '741101': { district: 'Nadia', state: 'West Bengal', area: 'Krishnanagar' },
  '741201': { district: 'Nadia', state: 'West Bengal', area: 'Ranaghat' },
  '741235': { district: 'Nadia', state: 'West Bengal', area: 'Kalyani' },
  '741245': { district: 'Nadia', state: 'West Bengal', area: 'Chakdaha' },

  // Darjeeling District (Rejected)
  '734001': { district: 'Darjeeling', state: 'West Bengal', area: 'Siliguri' },
  '734101': { district: 'Darjeeling', state: 'West Bengal', area: 'Darjeeling' },

  // Bardhaman / Purba Bardhaman / Paschim Bardhaman (Rejected)
  '713101': { district: 'Purba Bardhaman', state: 'West Bengal', area: 'Burdwan' },
  '713201': { district: 'Paschim Bardhaman', state: 'West Bengal', area: 'Durgapur' },
  '713301': { district: 'Paschim Bardhaman', state: 'West Bengal', area: 'Asansol' },

  // Medinipur (Rejected)
  '721101': { district: 'Paschim Medinipur', state: 'West Bengal', area: 'Midnapore' },
  '721401': { district: 'Purba Medinipur', state: 'West Bengal', area: 'Digha / Contai' },

  // Birbhum / Murshidabad / Malda (Rejected)
  '731101': { district: 'Birbhum', state: 'West Bengal', area: 'Suri' },
  '731204': { district: 'Birbhum', state: 'West Bengal', area: 'Bolpur / Shantiniketan' },
  '742101': { district: 'Murshidabad', state: 'West Bengal', area: 'Berhampore' },
  '732101': { district: 'Malda', state: 'West Bengal', area: 'Malda' },

  // =========================================================================
  // 5. OTHER INDIAN STATES (UNSUPPORTED / REJECTED)
  // =========================================================================
  '110001': { district: 'New Delhi', state: 'Delhi', area: 'Connaught Place' },
  '110016': { district: 'South Delhi', state: 'Delhi', area: 'Hauz Khas' },
  '110020': { district: 'South East Delhi', state: 'Delhi', area: 'Okhla' },
  '400001': { district: 'Mumbai', state: 'Maharashtra', area: 'Mumbai G.P.O. / Fort' },
  '400050': { district: 'Mumbai Suburban', state: 'Maharashtra', area: 'Bandra West' },
  '411001': { district: 'Pune', state: 'Maharashtra', area: 'Pune G.P.O.' },
  '560001': { district: 'Bengaluru Urban', state: 'Karnataka', area: 'MG Road' },
  '560034': { district: 'Bengaluru Urban', state: 'Karnataka', area: 'Koramangala' },
  '600001': { district: 'Chennai', state: 'Tamil Nadu', area: 'Chennai G.P.O.' },
  '500001': { district: 'Hyderabad', state: 'Telangana', area: 'Hyderabad G.P.O.' },
  '201301': { district: 'Gautam Buddha Nagar', state: 'Uttar Pradesh', area: 'Noida Sector 1' },
  '122001': { district: 'Gurugram', state: 'Haryana', area: 'Old Gurugram' },
};
