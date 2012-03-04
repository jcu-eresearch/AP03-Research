# File to convert all data dump files into a specific json format ready for importing into cake php app
#
# Produces a different output based on whether or not ARGV[0] is supplied.
# If no ARGV[0], all species are provided in a format as follows:
#
# {
#   'species': [
#     {
#       'name': 'aaa',
#       'sp_no': '1',
#       'occurrences': [
#         { 'latitude': '12.32232', 'longitude': '13.4223'  },
#         { 'latitude': '15.32232', 'longitude': '130.4223' }
#       ]
#     },
#     {
#       'name': 'aaa',
#       'sp_no': '2',
#       'occurrences': [
#         { 'latitude': '12.32232', 'longitude': '13.4223'  },
#         { 'latitude': '15.32232', 'longitude': '130.4223' }
#       ],
#     }
#   ]
# }
#
# If ARGV[0] is supplied, only information for that specific species is provided. Example outut for ARGV[0] => 1:
#
# {
#   'name': 'aaa',
#   'sp_no': '1',
#   'occurrences': [
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
	output = {}
	output['species'] = []
	all_species.each do |species_x|
		name    = species_x['names']
		sp_no   = species_x['SpNo']
		species_x_output = {}

		species_x_output['name'] = name
		species_x_output['sp_no'] = sp_no

		# Load the occurrences
		species_x_output['occurrences'] = get_occurrences(sp_no)

		output['species'] << species_x_output
	end
else
	output = {}
	all_species.each do |species_x|
		name    = species_x['names']
		sp_no   = species_x['SpNo']

		if sp_no == $target_species_id
			species_x_output = {}

			species_x_output['name'] = name
			species_x_output['sp_no'] = sp_no

			# Load the occurrences
			species_x_output['occurrences'] = get_occurrences(sp_no)

			output = species_x_output
			break
		end
	end
end

puts JSON.dump(output)
