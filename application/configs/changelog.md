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
* Added print audit support
* Added memjet device swaps into the system.
* Added tracking of users last valid login time
* Added the ability to mark master devices as part of a JIT Program.
* Added the ability to mark device instances as leased.

* Changed admin/Labor/Parts CPP and Energy Cost are now allowed to be 0

* Fixed the ability to delete dealers when they have a device swap attached
* Fixed the hardware quote from displaying incorrect numbers
* Fixed the health check jit graph colors and criteria
* Fixed sorting on the hardware library all devices page
* Fixed the alignment of text inside text fields for settings
* Fixed order of quotes/optimization reports will be from newest created to oldest

Version 1.4.4
=============
* Added a new bulk file imports into the system.
* Added a new interface for import and export files.
* Added a new report, Toner Vendor Gross Margin
* Added explanation of how dealer / system overrides work for labor and parts cost per page
* Added the ability for Master Device Administrators to approve toners.
* Added the ability for Master Device Administrators to edit manufacturers.
* Added the ability for users that are not administrators to create, edit and delete toners that are not system toners.
* Added the ability for users to create manufacturers.
* Added the ability to add names to the reports
* Added the ability to filter toner exports by manufacturer
* Added the ability to import leasing rates

* Changed look and feel of form / buttons to be more uniform across the application
* Changed look and feel of manufacturers edit / create forms
* Changed mapping no longer closes modal after saving.
* Changed toners now are assigned automatically after creating them on the assigned toners page.

* Fixed bulk file pricing not being able to update by percentage
* Fixed exclusion of devices for hardware optimization not working.
* Fixed machine compatibility from not showing when assigning toners that were not yet saved.
* Fixed quote device groups not being saved
* Fixed quote settings form multi select width bug
* Fixed the ability to save user profiles
* Fixed the alignment of device swaps reason textbox
* Fixed the assigned toners jqgrid from not being able to be sorted

Version 1.4.3
=============
* Fixed issue cost delta would not include parts and labor cost per page (display only)

* Removed part types from toners. Toner vendors are now used instead.

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
* Changed hardware optimization to use ajax for a better ux
* Changed how toner pricing imports handle finding a toner
* Changed more settings now have the ability to handle 0 as a value
* Changed order of settings fields to be more uniform
* Changed service cost per page to a combination of parts and labor
* Changed user names to be email addresses

* Fixed bugs with report generation
* Fixed issue with cost delta not showing up when switching hardware optimization device back to keep
* Fixed issue where service cost was sometimes not being applied to hardware optimization replacements
* Fixed issues with update pricing
* Fixed replacement devices did not have service cost per page
* Fixed titles inside customer facing hardware optimization report to be less ambiguous

* Removed customer pricing config for hardware optimization
* Removed dealer margin for hardware optimization

Version 1.3.1
=============
* Fixed bugs with toner management

* Removed transfer reports functionality

Version 1.3.0
=============
* Added support for xerox csv file imports

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