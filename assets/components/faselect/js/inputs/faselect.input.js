// Wrap your stuff in this module pattern for dependency injection
(function ($, ContentBlocks) {
    // Add your custom input to the fieldTypes object as a function
    // The dom variable contains the injected field (from the template)
    // and the data variable contains field information, properties etc.
    ContentBlocks.fieldTypes.faselect = function(dom, data) {
        var input = {};

        input.init = function () {
            // Generate the heading dropdown based on field configuration
            var select = dom.find('.contentblocks-field-faselectinput select');
//console.log(select);            
            // make a blank option at the top. though this disables the ability to make it required (in an obvious way)
            select.append('<option value=""></option>');
            
            // get the data set
            var faOutputPath = MODx.config['faselect.output_path'],
                faOutputFile = MODx.config['faselect.output_filename'];
                
            if (!faOutputPath) faOutputPath = MODx.config['assets_url'] + 'components/faselect/js/';
            if (!faOutputFile) faOutputFile = 'faselectinputoptions.json';
            
            if (faOutputPath.substring(0, MODx.config['base_url'].length) === MODx.config['base_url']) { 
                faOutputPath = MODx.config['base_url'] + faOutputPath.substring(MODx.config['base_url'].length);
            }
            $.get(faOutputPath + faOutputFile, function(d) {
//console.log(d);
                $.each(d, function(k, v) {
                    var selected = (v === data.value) ? 'selected' : '';
                    select.append('<option value="' + v + '" ' + selected + '>' + k + '</option>');
                
                });
            
            });

        };

        input.getData = function () {
            var selected = dom.find('.contentblocks-field-faselectinput select option:selected'),
                value = selected.val(),
                display = selected.text();
                
            return {
                value: value,
                display: display                
            };
        };

        input.confirmBeforeDelete = function() {
            var inputData = input.getData(),
            hasClass = inputData.value != data.properties.default_value
            return hasClass;
        };

        return input;
    }
})(vcJquery, ContentBlocks);