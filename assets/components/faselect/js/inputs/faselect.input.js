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
            
            // make a blank option at the top. though this disables the ability to make it required (in an obvious way)
            select.append('<option value=""></option>');
            
            // get the data set
            var json = false;
            $.get('/assets/components/faselect/js/faselectinputoptions.json', function(d) {
            
                json = JSON.parse(d, function(k, v) {
                
                    select.append('<option value="' + v + '">' + k + '</option>');
                
                });
            
            });
            
            if (data.value) {
                select.val(data.value);
            }
            else {
                var def = data.properties.default_value || '';
                select.val(def);
            }

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