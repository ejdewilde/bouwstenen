$(document).ready(function ()
{
    var allOptions = $('#groupSelect option')
    $('#locationSelect').change(function ()
    {
        $('#groupSelect option').remove()
        var classN = $('#locationSelect option:selected').prop('class');
        var opts = allOptions.filter('.' + classN);
        $('#groupSelect option').appendTo();
        var o = new Option("-- selecteer groep --", "0");
        /// jquerify the DOM object 'o' so we can use the html method
        //$(o).html("option text");
        $("#groupSelect").append(o);

        $.each(opts, function (i, j)
        {
            $(j).appendTo('#groupSelect');
        });
        $("#groupSelect").val("0");
    });
    $('#startSm').on('click', function ()
    {
        $("#naar_sm").submit();

    });
});