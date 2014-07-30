
var map;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var hash = getVars();
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng( 45, -122), //hash['lat'], hash['lng']),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(map_canvas, map_options);
}

function getVars() {
        var query = $.url().attr('query');
        var args = query.split("&");
        var hash = {};
        for (var i = 0; i < args.length; ++i ) {
                var set = args[i].split("=");
                hash[set[0]] = set[1];
        }
        return hash;
}

$(document).ready(function() {
    var hash = getVars();
    var requestURL="http://ec2-54-213-36-220.us-west-2.compute.amazonaws.com/api/office/" + hash['id'];
    var $resultsArea = $('#position-title');
    $.jsonp({
        url: requestURL,
        success: function(data) {
            console.log(data);
            var out = data.office_positions[0].position_name;
            $resultsArea.html(out);
            
            $resultsArea = $('#reqSal');
            out = '<dt>Age Requirement</dt><dd>' + data.office.age_requirements + '</dd>';
            out += '<dt>Residency Requirements</dt><dd>' + data.office.res_requirements + '</dd>';
            out += '<dt>Professional Requirements</dt><dd>' + data.office.prof_requirements + '</dd>';
            out += '<dt>Salary</dt><dd>' + data.office.salary + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#timeFra');
            out = '<dt>Duration of Office</dt><dd>' + data.office.term_length_months + ' Months</dd>';
            out += '<dt>Next Election</dt><dd>' + data.office_positions[0].next_election + '</dd>';
            out += '<dt>Filing Deadline</dt><dd>' + data.office_positions[0].filing_deadline + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#basDut');
            out = '<dt>Responsibilities</dt><dd>' + data.office.responsibilities + '</dd>';
            out += '<dd>' + data.office.notes + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#filDoc');
            out =  '';
            var oDocs = data.office_docs
            for (var i = 0; i < oDocs.length; ++i) {
                out += '<dt>' + oDocs[i].name + '</dt><dd>' + oDocs[i].link + '</dd>';
            }
            var eDocs = data.office_positions[0].election_div_docs
            for (var i = 0; i < eDocs.length; ++i) {
                out += '<dt>' + eDocs[i].name + '</dt><dd>' + eDocs[i].link + '</dd>';
            }
            if (out == '') {
                out = '<dt>""</dt><dd>""</dd>';
            }
            $resultsArea.html(out);
            
            $resultsArea = $('#wheHow');
            out = '<dt>District</dt><dd>' + data.office_positions[0].district.name + '</dd>';
            out += '<dt>Election Division</dt><dd>' + data.office_positions[0].election_div.name + '</dd>';
            out += '<dt>Physical Address</dt><dd>' + data.office_positions[0].election_div.phys_addr_addr1 + '</dd>' ;
            out += '<dd>' + data.office_positions[0].election_div.phys_addr_addr2 + '</dd><dd>' + data.office_positions[0].election_div.phys_addr_city + '</dd>';
            out += '<dd>' + data.office_positions[0].election_div.phys_addr_state + '</dd><dd>' + data.office_positions[0].election_div.phys_addr_zip + '</dd>';
            out += '<dt>Mailing Address</dt><dd>' + data.office_positions[0].election_div.mail_addr_addr1 + '</dd>';
            out += '<dd>' + data.office_positions[0].election_div.mail_addr_addr2 + '</dd><dd>' + data.office_positions[0].election_div.mail_addr_city + ', ' + data.office_positions[0].election_div.mail_addr_state + '</dd>';
            out += '<dd>' + data.office_positions[0].election_div.mail_addr_zip + '</dd>';
            out += '<dt>Phone Number</dt><dd>' + data.office_positions[0].election_div.phone + '</dd>';
            out += '<dt>Website</dt><dd>' + data.office_positions[0].election_div.website + '</dd>';
            out += '<dt>Filling Fee</dt><dd>' + data.office.filing_fee + '</dd>';
            $resultsArea.html(out);
        }
    });
});

