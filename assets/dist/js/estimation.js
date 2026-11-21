$(document).ready(function () {
    $(document).on('change', 'select.cell-editor', function () {
        var $select = $(this);
        // This 'column' variable is specifically for the cell that triggered the change.
        // We'll need to get the column name for *all* cells differently.
        var changedColumn = $select.closest('td').data('column');

        // Check if the changed column is 'lead_status' and its new value is 'successful'
        if (changedColumn === 'lead_status' && $select.val().toLowerCase() === 'successful') {
            var $row = $select.closest('tr');
            var rowData = {};

            // Get the table headers to map column names to their order
            var $headers = $row.closest('table').find('thead th');
            var headerMap = {};
            $headers.each(function(index) {
                // Clean up the header text to match your PHP $editableCols array if possible
                // For example, "estimation1 hours" -> "estimation1_hours"
                let headerText = $(this).text().trim().toLowerCase().replace(/ /g, '_');
                // Handle the 'Actions' column which doesn't correspond to a DB field
                if (headerText !== 'actions') {
                    headerMap[index] = headerText;
                }
            });

            // Iterate over ALL <td> elements in the current row
            $row.find('td').each(function (index) {
                var $cell = $(this);
                var val;
                var col = headerMap[index]; // Get column name from header map

                // Skip if this column doesn't have a mapped header (e.g., 'Actions' column)
                if (!col) {
                    return true; // continue to the next iteration
                }

                // Check if this is an editable cell
                if ($cell.hasClass('editable-cell')) {
                    var $editor = $cell.find('.cell-editor');
                    var $displayDiv = $cell.find('.cell-display');

                    if ($editor.length && $editor.is(':visible')) { // If editor exists and is visible
                        val = $editor.val();
                    } else if ($displayDiv.length) { // If display div exists
                        val = $displayDiv.text().trim();
                    } else { // Fallback if somehow neither is found
                        val = $cell.text().trim();
                    }
                } else {
                    // For non-editable cells, just get the text content
                    val = $cell.text().trim();
                }

                if (col === 'id' && $cell.data('row-id')) {
                    val = $cell.data('row-id');
                }

                rowData[col] = val;
            });

          
            rowData['id'] = $row.find('.editable-cell').first().data('row-id');


            console.log("Sending row data:", rowData);

            $.post('estimation_success_handler.php', rowData, function (response) {
                console.log('Data sent successfully!');
                console.log(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Failed to send data. Status:', textStatus, 'Error:', errorThrown);
                if (jqXHR.responseText) {
                    console.error('Server response:', jqXHR.responseText);
                }
            });
        }
    });
});