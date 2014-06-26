# Report Standards

## Title Pages

* Report title should be center-aligned
* Dates should be formatted (without time) and be under the address

## Headings

1. Major section headings (ex. in Assessment report, 'Analysis: Service Supplies Logistics'):
    * size 15
    * color B8CCE3
    * left-aligned

2. Subheadings (ex. in Assessment report, 'Printing Device Management Breakdown')
    * size 12
    * color FFFFFF
    * have a background color of B8CCE3
    * are italicized

## Tables

### Table Styling

* Use alternating rows of light grey and white to distinguish rows from each other (even EFEFEF, odd FCFCFC)
* Row headers should be reprinted on every new page that continues a table
* Row headers should have light blue background (B8CCE3)

### Row Styling

* Rows do not split across pages (i.e. 'cantSplit' => true)

### Cell Styling

* Column headings are centered and aligned to the top of the cell (rather than vertically-aligned in the center - allows for cleaner multiple lines)
* Generally, alphanumeric-text is *left-aligned* and numerical data is *right-aligned*
* Small tables (ex. Assessment tables, some have 3 rows on purpose) can center data for emphasis


### Table Content

When creating tables - the standard line-up of columns is:

* Device (alternatively, can be split into two columns: Manufacturer *then* Model)
* Serial Number / IP Address (alternatively, they may be split into two columns: Serial Number *then* IP Address)
* JIT Compatible
* Mono AMPV
* Color AMPV
* Life Page Count
* Device Age (where each integer should be followed by 'y' to indicate years)
* Mono CPP
* Color CPP
* Monthly Cost
* Percentage Values (Mono, Color, Monthly)
* *There are other tables that need to have their order figured out - ex. Healthcheck Fleet Attributes tables*

When indicating a null or not applicable value in a table, use a dash '-' instead of 'N/A' or a blank

#### Numerical Values in Tables

* Cost-per-page values have 4 decimal places
* All other currency values should have 2 decimal places (there may be exceptions)
* Percentages are case-by-case?
* ```$this->currency``` provides 2 decimal places only
* ```$this->formatCostPerPage``` provides 4 decimal places
* ```number_format($value,3)``` allows for decimal place specification


### Graphs

* Titles should have the first letter be uppercase (except in the case of small words, like 'the, of, per, etc..')
* Legends should only have the first letter be uppercase (except in the case of formal name references?)


### Pluralized Content Issues

* Currently, we're using ```sprintf()``` to show correct plural/singular words in sentences.
* Only need to worry about singular/plural versions when the number of objects is likely to be both (ex. Number of networked devices is almost always going to be 2+, as well as number of pages printed, so don't need to add code for a single instance - tldr: Look at the context of the number to figure out when it's needed).
