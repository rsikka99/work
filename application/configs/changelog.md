*[A3]: Large Format Printing (11x17)
*[AMPV]: Average Monthly Page Volume
*[CPP]: Cost Per Page
*[CSV]: Comma-separated values
*[JIT]: Just In Time
*[OEM]: Original Equipment Manufacturer
*[SQL]: Structured Query Language
*[RMS]: Remote Monitoring System
*[UX]: User Experience

Version 1.4.13
==============
* Fixed issue with dealer toner preferences not propagating
* Fixed issue with false detection of color capabilities on PrintFleet 3.x reports that aren't generated via SQL which caused toner level data to be incorrect
* Fixed issue with string based RMS Model IDs such as Print Audit IDs not mapping more than 1 device at a time

Version 1.4.12
==============
* Added the ability to group devices in a Print Audit upload
* Fixed issue with long customer names on graphs
* Fixed issue that color devices do not always save successfully due to an issue with the toner configuration

Version 1.4.11
==============
* Added custom contracts (not user customizable, must be requested)
* Added support for **PrintFleet 3.x** csv uploads
* Added customizable branding found under **Admin -> Branding**
    * Graph Colors
    * Report Colors
    * Report Names
* Changed lease rates to be configurable anywhere between 0.00001 and 1.0 no matter how many decimal places are used
* Changed quote report names to include customer name
* Changed all customer facing reports to have a proper title page
* Changed all HTML reports to be in sync with their DOCX counterparts
* Fixed issue with logos getting distorted when uploaded
* Fixed issue with transparent PNG files getting a solid background
* Fixed issue where release date on devices would not save properly
* Fixed issue where toners could not be edited due to a duplicate sku error
* Fixed graphs to be consistent throughout reports. Their colors can be changed within the Branding aspect of the software
* Fixed typos and inconsistencies in all reports
* Removed Lorem Ipsum from Hardware Optimization Customer Facing Report

Version 1.4.10
==============
* Added markdown parsing of the changelog
* Added **NER Data** as a RMS vendor
* Added more information to the device popups on the device summary and hardware optimization pages
* Added the ability to change hardware device configurations
* Changed the order of the "Minimum Page Count" and "Maximum Page Count" when dealing with a device swap in the "All Devices" editor
* Changed the "recommended maximum monthly page volume" to be the highest OEM yield. Maximum life page volume is now the highest OEM yield * 36
* Fixed device features bulk export
* Fixed device management date popup being hidden in the background as well as how the date is shown
* Fixed device swap not showing an error when trying to unset the reason for a category
* Fixed formatting of cost/price for consistency
* Fixed formatting of CPP for consistency
* Fixed formatting of page volume for consistency
* Fixed mapping large quantity of devices (150+ of a single type)
* Fixed numbers and formatting on the toner gross vendor margin report
* Fixed report names to generate properly and consistently throughout the system
* Fixed selection of the leasing provider not loading ajax properly
* Fixed the back button in the bulk device pricing/uploads area
* Fixed the lease buyback report not working
* Fixed the wrong toner preferences being used in the hardware optimization in some cases
* Fixed toners not showing up as assigned when creating them for a device

Version 1.4.9
=============
* Added assumption inside the solution
* Added customer pricing import
* Fixed bulk device pricing imports requiring a client being selected
* Fixed forgot password interface producing errors because of the wrong layout
* Fixed toner matchup import
* Fixed typo on download buttons for excel files

Version 1.4.8
=============
* Added assessment fleet attributes report
* Added isA3, isDuplex, reportsTonerLevels into rms upload
* Added JIT Compatibility flag on device features export
* Added Managed checkbox on the device summary page
* Added toner management in the hardware library
* Added toner SKUs to gross margin report
* Changed health check green features graphs to show number of devices instead of a percentage
* Changed health check water per page value from 0.121675 to 2.6 gallons
* Removed duty cycle from the system
* Removed JIT Compatibility checkbox on rms upload as it can only be set on master devices. Shows Yes/No instead now
* Removed the hybrid solution from the solution report

Version 1.4.7
=============
* Changed backend storage
* Fixed issues with toner data
* Fixed many backend bugs

Version 1.4.6
=============
* Added memjet optimize for select dealers

Version 1.4.5
=============
* Added A3 support for master devices
* Added Print Audit support
* Added memjet device swaps into the system
* Added tracking of users last valid login time
* Added the ability to mark master devices as part of a JIT Program
* Added the ability to mark device instances as leased
* Changed admin/labor/parts CPP and energy cost are now allowed to be 0
* Fixed the ability to delete dealers when they have a device swap attached
* Fixed the hardware quote from displaying incorrect numbers
* Fixed the health check JIT graph colors and criteria
* Fixed sorting on the hardware library all devices page
* Fixed the alignment of text inside text fields for settings
* Fixed order of quote and optimization reports will be from newest created to oldest

Version 1.4.4
=============
* Added a new bulk file imports into the system
* Added a new interface for import and export files
* Added a new report, Toner Vendor Gross Margin
* Added explanation of how dealer / system overrides work for labor and parts CPP
* Added the ability for Master Device Administrators to approve toners
* Added the ability for Master Device Administrators to edit manufacturers
* Added the ability for users that are not administrators to create, edit and delete toners that are not system toners
* Added the ability for users to create manufacturers
* Added the ability to add names to the reports
* Added the ability to filter toner exports by manufacturer
* Added the ability to import leasing rates
* Changed look and feel of form / buttons to be more uniform across the application
* Changed look and feel of manufacturers edit / create forms
* Changed mapping no longer closes modal after saving
* Changed toners now are assigned automatically after creating them on the assigned toners page
* Fixed bulk file pricing not being able to update by percentage
* Fixed exclusion of devices for hardware optimization not working
* Fixed machine compatibility from not showing when assigning toners that were not yet saved
* Fixed quote device groups not being saved
* Fixed quote settings form multi select width bug
* Fixed the ability to save user profiles
* Fixed the alignment of device swaps reason text box
* Fixed the assigned toners grid from not being able to be sorted

Version 1.4.3
=============
* Fixed issue cost delta would not include parts and labor CPP (display only)
* Removed part types from toners. Toner vendors are now used instead

Version 1.4.2
=============
* Added the ability to export replacement devices to a quote
* Fixed issue where the quote menu would not show up
* Fixed issue where pages were taken into account when calculating the monthly lease price

Version 1.4.1
=============
* Fixed issue with hardware optimization settings not saving correctly

Version 1.4.0
=============
* Added a new settings management interface
* Added a new look and feel
* Added ability to handle managed and un-managed devices from an upload
* Added ability for dealers to manage device swap reasons
* Added cost analysis report
* Added dealer support for reports
* Added device swaps for hardware optimization
* Added integration with hardware optimization
* Added single sign-on access for all components
* Added support for management of other users part of your organization
* Added the forgot password system
* Added the health check report
* Changed assessments, hardware optimization, hardware quotes and health checks
* Changed hardware optimization to use ajax for a better UX
* Changed how toner pricing imports handle finding a toner
* Changed more settings now have the ability to handle 0 as a value
* Changed order of settings fields to be more uniform
* Changed service CPP to a combination of parts and labor
* Changed user names to be email addresses
* Fixed bugs with report generation
* Fixed issue with cost delta not showing up when switching hardware optimization device back to keep
* Fixed issue where service cost was sometimes not being applied to hardware optimization replacements
* Fixed issues with update pricing
* Fixed replacement devices did not have service CPP
* Fixed titles inside customer facing hardware optimization report to be less ambiguous
* Removed customer pricing config for hardware optimization
* Removed dealer margin for hardware optimization

Version 1.3.1
=============
* Fixed bugs with toner management
* Removed transfer reports functionality

Version 1.3.0
=============
* Added support for Xerox CSV file imports
* Removed old 'ticketing' system for hardware additions

Version 1.0.0
=============
* Added support for the assessment, gross margin, and solution reports
* Changed permissions to allow role management
* Changed proposal generator workflow

Version 0.5.0
=============
* Added the first functional version of the quote generator

Version 0.1.0
=============
* Added first preview of the quote generator
* Added new type of permission system to allow finer grain permissions
* Added very minimal support for assessment features