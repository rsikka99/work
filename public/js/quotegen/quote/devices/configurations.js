function loadNewConfiguration()
{
    var configurationTable = $('#configurationsTable');
    configurationTable.load(TMTW_BASEURL + 'quotegen/quote_devices/configurations-table',
        {
            'configurationId' : $('#configurationId').val()
        }
    );
}