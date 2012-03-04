# File to convert all data dump files into a specific json format ready for importing into cake php app
#
# ARGV[0] defines the target species, only information for that specific species is provided. Example outut for ARGV[0] => 1:
#
# {
#   'Species': {
#     'name': 'aaa',
#     'sp_no': '1'
#   },
#   'Occurrence': [
#     { 'latitude': '12.32232', 'longitude': '13.4223'  },
#     { 'latitude': '15.32232', 'longitude': '130.4223' }
#   ]
# }

require 'rubygems'
require 'json'

$target_species_id = ARGV[0]

$this_file = File.dirname(__FILE__)
$data_dump_folder = File.join($this_file, 'data_dump')
$species_file = File.join($data_dump_folder, 'species.json')

def get_occurrences sp_no
	occurrences_file = File.join($data_dump_folder, "#{sp_no}.json")
	occurrences_file_contents = ''
	File.open(occurrences_file, 'r') do |f|
		occurrences_file_contents = f.read
	end

	json_parsed_occurrences = JSON.parse(occurrences_file_contents)
	# Looks like:
	# {"Locations"=>[{"col"=>"2", "usr_val"=>"0", "Longitude"=>"145.50000000", "Latitude"=>"-43.50000000"}, 

	locations = json_parsed_occurrences['Locations']

	output = []
	locations.each do |location|
		output << { 'latitude' => location['Latitude'], 'longitude' => location['Longitude'] }
	end

	return output
end

species_file_contents = ''
File.open($species_file, 'r') do |f|
	species_file_contents = f.read
end

json_parsed = JSON.parse(species_file_contents)
all_species = json_parsed['spp']

output = nil
if $target_species_id.nil?
	raise "ARGV[0] not supplied. Must be the id of a species"
else
	output = {}
	all_species.each do |species_x|
		name    = species_x['names']
		sp_no   = species_x['SpNo']

		if sp_no == $target_species_id
			species_x_output = {}

			species_x_output['name'] = name
			species_x_output['sp_no'] = sp_no

			output['Species'] = species_x_output

			output['Occurrence'] = get_occurrences(sp_no)
			break
		end
	end
end

puts JSON.dump(output)
