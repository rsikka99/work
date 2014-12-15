define(function ()
{
    var Utility = {};

    /**
     * Gets the index of a column by name
     *
     * @param grid
     * @param columnName
     * @returns {number}
     */
    Utility.getColumnSrcIndexByName = function (grid, columnName)
    {
        var colModelColumnName = grid.jqGrid('getGridParam', 'colModel');
        var currentIndex = 0;
        var indexOfColumn = -1;

        for (var i = 0; i < colModelColumnName.length; i++)
        {
            if (colModelColumnName[i].name === columnName)
            {
                indexOfColumn = currentIndex;
                break;
            }

            if (colModelColumnName[i].name !== 'rn'
                && colModelColumnName[i].name !== 'cb'
                && colModelColumnName[i].name !== 'subgrid')
            {
                currentIndex++;
            }
        }

        return indexOfColumn;
    };

    return Utility;
});