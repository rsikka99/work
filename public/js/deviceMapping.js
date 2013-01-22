/**
 * show_mapped holds the visibility state of the bottom jqgrid
 */
var show_mapped = false;

$(function() {
	// find center screen for modal popup
	var sTop = ($(window).height() / 2) - 100;
	var sLeft = ($(window).width() / 2) - 200;

	/***********************************************************************************************************************************************************
	 * UNMAPPED GRID
	 **********************************************************************************************************************************************************/
	jQuery("#results_list").jqGrid(
			{
				url : TMTW_BASEURL + 'data/devicemappinglist',
				datatype : 'json',
				colModel : [ {
					tag : 0,
					width : 10,
					name : 'id',
					index : 'id',
					hidden : true,
					label : 'RMS Upload Row Id',
					title : false,
					sortable : false
				}, {
					tag : 1,
					width : 10,
					name : 'rmsModelId',
					index : 'rmsModelId',
					hidden : true,
					label : 'RMS Model Number',
					title : false,
					sortable : false
				}, {
					tag : 2,
					width : 10,
					name : 'rmsProviderId',
					index : 'rmsProviderId',
					hidden : true,
					label : 'RMS Provider',
					title : false,
					sortable : false
				}, {
					tag : 3,
					width : 40,
					name : 'count',
					index : 'count',
					label : 'Count',
					title : false,
					sortable : false,
					align : 'center'
				}, {
					tag : 4,
					width : 200,
					name : 'imported_devicename',
					index : 'imported_devicename',
					label : 'Import Printer Name',
					title : false,
					sortable : false
				}, {
					tag : 5,
					width : 10,
					name : 'mapped_to_id',
					index : 'mapped_to_id',
					label : 'Mapped To ID',
					hidden : true,
					sortable : false
				}, {
					tag : 6,
					width : 10,
					name : 'mapped_to_modelname',
					index : 'mapped_to_modelname',
					label : 'Mapped To ModelName',
					hidden : true,
					sortable : false
				}, {
					tag : 7,
					width : 10,
					name : 'mapped_to_manufacturer',
					index : 'mapped_to_manufacturer',
					label : 'Mapped To Manufacturer',
					hidden : true,
					sortable : false
				}, {
					tag : 8,
					width : 250,
					name : 'master_device_id',
					index : 'master_device_id',
					label : 'Master Printer Name',
					sortable : false,
					align : 'center'
				}, {
					tag : 9,
					width : 10,
					name : 'is_added',
					index : 'is_added',
					label : 'Is Added',
					hidden : true,
					title : false,
					sortable : false
				}, {
					tag : 10,
					width : 10,
					name : 'is_leased',
					index : 'is_leased',
					label : 'Is Leased',
					hidden : true,
					title : false,
					sortable : false
				}, {
					tag : 11,
					width : 50,
					name : 'action',
					index : 'action',
					label : 'Action',
					title : false,
					sortable : false,
					align : 'center'
				} ],
				jsonReader : {
					repeatitems : false,
				},
				width : 615,
				height : 400,
				rowNum : -1,
				gridComplete : function() {

					// Get the grid object (cache in variable)
					var grid = $(this);
					var ids = grid.getDataIDs();

					for ( var i = 0; i < ids.length; i++) {
						// Get the data so we can use and manipualte it.
						var row = grid.getRowData(ids[i]);

						// This is what toggles the 'master printer
						// name' field between the auto complete text
						// box and the 'Click to Remove' text
						if (row.is_added == 'true') {
							// Display message instead of dropdown
							row.master_device_id = '&nbsp;New Printer Added (<a href="javascript: void(0);" onclick="javascript: remove_device(' + row.id + ');">Click to Remove</a>)';
							row.action = '<input style="width:35px;" title="Edit Printer"    type="button" onclick="javascript: add_device(' + row.id + ');" value="Edit" />';

						} else {
							master_device_dropdown = '';
							master_device_dropdown += '<input type="hidden" name="hdnDevicesPfId' + row.id + '" id="hdnDevicesPfId' + row.id + '" class="devicesPfId" value="' + row.id + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesValue' + row.id + '" id="hdnMasterDevicesValue' + row.id + '" class="masterDeviceId" value="' + row.mapped_to_id + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesText' + row.id + '" id="hdnMasterDevicesText' + row.id + '" class="masterDeviceName" value="' + row.mapped_to_modelname + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesManufacturer' + row.id + '" id="hdnMasterDevicesManufacturer' + row.id + '" class="manufacturerName" value="' + row.mapped_to_manufacturer + '" />';
							master_device_dropdown += '<input type="text" name="txtMasterDevices' + row.id + '" id="txtMasterDevices' + row.id + '" size="50" class="autoCompleteDeviceName" value="' + row.mapped_to_manufacturer + ' '
									+ row.mapped_to_modelname + '" />';

							row.master_device_id = master_device_dropdown;
							row.action = '<input style="width:35px;" title="Add New Printer" type="button" onclick="javascript: add_device(' + row.id + ');" value="Add" />';
						}

						// Put our new data back into the grid
						grid.setRowData(ids[i], row);

						// Setup autocomplete for our textbox
						$(".autoCompleteDeviceName").autocomplete({
							source : function(request, response) {
								$.ajax({
									url : TMTW_BASEURL + "data/getmodels",
									dataType : "json",
									data : {
										searchText : request.term
									},
									success : function(data) {
										response($.map(data, function(item) {
											return {
												value : item.label,
												id : item.value,
												label : item.label,
												manufacturer : item.manufacturer
											};
										}));
									}
								});
							},
							minLength : 0,
							select : function(event, ui) {
								$(this).parent().find("input.masterDeviceId")[0].value = ui.item.id;
								$(this).parent().find("input.masterDeviceName")[0].value = ui.item.label;
								$(this).parent().find("input.manufacturerName")[0].value = ui.item.manufacturer;
							},
							open : function(event, ui) {
								var termTemplate = '<strong>%s</strong>';
								var autocompleteData = $(this).data('autocomplete');
								autocompleteData.menu.element.find('a').each(function() {
									var label = $(this);
									var regex = new RegExp(autocompleteData.term, "gi");
									label.html(label.text().replace(regex, function(matched) {
										return termTemplate.replace('%s', matched);
									}));
								});
							},
							change : function(event, ui) {
								var parent = $(this).parent();
								var textValue = $.trim(this.value);
								var rmsUploadRowId = this.id.replace("txtMasterDevices", "");
								var masterDeviceId = $.trim(parent.find("input.masterDeviceId")[0].value);
								var deviceName = $.trim(parent.find("input.masterDeviceName")[0].value);

								/*
								 * Populate the text field if the user was auto completing, or clear it out if they were deleting the text
								 */
								if (textValue) {
									// If the device id is not set, then we reset to blank
									if (!masterDeviceId) {
										textValue = "";

									} else {
										// Set the name to the device name
										textValue = deviceName;

									}
									this.value = textValue;
									set_mapped(rmsUploadRowId, masterDeviceId);
								} else {
									parent.find("input.masterDeviceId")[0].value = "";
									parent.find("input.masterDeviceName")[0].value = "";
									parent.find("input.manufacturerName")[0].value = "";
									this.value = textValue;
									set_mapped(rmsUploadRowId, 0);
								}

							}
						});
					}
				},
				editurl : 'dummy.php'
			});

	jQuery("#results_list").jqGrid('navGrid', '#results_pager', {
		add : false,
		del : false,
		edit : false,
		refresh : false,
		search : false
	}, {
		closeAfterEdit : true,
		recreateForm : true,
		closeOnEscape : true,
		width : 400,
		top : sTop,
		left : sLeft
	}, {
		closeAfterAdd : true,
		recreateForm : true,
		closeOnEscape : true,
		width : 400,
		top : sTop,
		left : sLeft
	}, {}, {}, {});

	/***********************************************************************************************************************************************************
	 * MAPPED GRID
	 **********************************************************************************************************************************************************/
	jQuery("#mapped_list").jqGrid(
			{
				url : '',
				datatype : 'json',
				colModel : [ {
					tag : 0,
					width : 10,
					name : 'id',
					index : 'id',
					hidden : true,
					label : 'RMS Upload Row Id',
					title : false,
					sortable : false
				}, {
					tag : 1,
					width : 10,
					name : 'rmsModelId',
					index : 'rmsModelId',
					hidden : true,
					label : 'RMS Model Number',
					title : false,
					sortable : false
				}, {
					tag : 2,
					width : 10,
					name : 'rmsProviderId',
					index : 'rmsProviderId',
					hidden : true,
					label : 'RMS Provider',
					title : false,
					sortable : false
				}, {
					tag : 3,
					width : 40,
					name : 'count',
					index : 'count',
					label : 'Count',
					title : false,
					sortable : false,
					align : 'center'
				}, {
					tag : 4,
					width : 200,
					name : 'imported_devicename',
					index : 'imported_devicename',
					label : 'Import Printer Name',
					title : false,
					sortable : false
				}, {
					tag : 5,
					width : 10,
					name : 'mapped_to_id',
					index : 'mapped_to_id',
					label : 'Mapped To ID',
					hidden : true,
					sortable : false
				}, {
					tag : 6,
					width : 10,
					name : 'mapped_to_modelname',
					index : 'mapped_to_modelname',
					label : 'Mapped To ModelName',
					hidden : true,
					sortable : false
				}, {
					tag : 7,
					width : 10,
					name : 'mapped_to_manufacturer',
					index : 'mapped_to_manufacturer',
					label : 'Mapped To Manufacturer',
					hidden : true,
					sortable : false
				}, {
					tag : 8,
					width : 250,
					name : 'master_device_id',
					index : 'master_device_id',
					label : 'Master Printer Name',
					sortable : false,
					align : 'center'
				}, {
					tag : 9,
					width : 10,
					name : 'is_added',
					index : 'is_added',
					label : 'Is Added',
					hidden : true,
					title : false,
					sortable : false
				}, {
					tag : 10,
					width : 10,
					name : 'is_leased',
					index : 'is_leased',
					label : 'Is Leased',
					hidden : true,
					title : false,
					sortable : false
				}, {
					tag : 11,
					width : 50,
					name : 'action',
					index : 'action',
					label : 'Action',
					title : false,
					sortable : false,
					align : 'center'
				} ],
				jsonReader : {
					repeatitems : false,
				},
				width : 615,
				height : 400,
				rowNum : -1,
				gridComplete : function() {

					// Get the grid object (cache in variable)
					var grid = $(this);
					var ids = grid.getDataIDs();

					for ( var i = 0; i < ids.length; i++) {
						// Get the data so we can use and manipualte it.
						var row = grid.getRowData(ids[i]);

						// This is what toggles the 'master printer
						// name' field between the auto complete text
						// box and the 'Click to Remove' text
						if (row.is_added == true) {
							// Display message instead of dropdown
							row.master_device_id = '&nbsp;New Printer Added (<a href="javascript: void(0);" onclick="javascript: remove_device(' + row.id + ');">Click to Remove</a>)';
							row.action = '<input style="width:35px;" title="Edit Printer"    type="button" onclick="javascript: add_device(' + row.id + ');" value="Edit" />';

						} else {
							master_device_dropdown = '';
							master_device_dropdown += '<input type="hidden" name="hdnDevicesPfId' + row.id + '" id="hdnDevicesPfId' + row.id + '" class="devicesPfId" value="' + row.id + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesValue' + row.id + '" id="hdnMasterDevicesValue' + row.id + '" class="masterDeviceId" value="' + row.mapped_to_id + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesText' + row.id + '" id="hdnMasterDevicesText' + row.id + '" class="masterDeviceName" value="' + row.mapped_to_modelname + '" />';
							master_device_dropdown += '<input type="hidden" name="hdnMasterDevicesManufacturer' + row.id + '" id="hdnMasterDevicesManufacturer' + row.id + '" class="manufacturerName" value="' + row.mapped_to_manufacturer + '" />';
							master_device_dropdown += '<input type="text" name="txtMasterDevices' + row.id + '" id="txtMasterDevices' + row.id + '" size="50" class="autoCompleteDeviceName" value="' + row.mapped_to_manufacturer + ' '
									+ row.mapped_to_modelname + '" />';

							row.master_device_id = master_device_dropdown;
							row.action = '<input style="width:35px;" title="Add New Printer" type="button" onclick="javascript: add_device(' + row.id + ');" value="Add" />';
						}

						// Put our new data back into the grid
						grid.setRowData(ids[i], row);

						// Setup autocomplete for our textbox
						$(".autoCompleteDeviceName").autocomplete({
							source : function(request, response) {
								$.ajax({
									url : TMTW_BASEURL + "data/getmodels",
									dataType : "json",
									data : {
										searchText : request.term
									},
									success : function(data) {
										response($.map(data, function(item) {
											return {
												value : item.label,
												id : item.value,
												label : item.label,
												manufacturer : item.manufacturer
											};
										}));
									}
								});
							},
							minLength : 0,
							select : function(event, ui) {
								$(this).parent().find("input.masterDeviceId")[0].value = ui.item.id;
								$(this).parent().find("input.masterDeviceName")[0].value = ui.item.label;
								$(this).parent().find("input.manufacturerName")[0].value = ui.item.manufacturer;
							},
							open : function(event, ui) {
								var termTemplate = '<strong>%s</strong>';
								var autocompleteData = $(this).data('autocomplete');
								autocompleteData.menu.element.find('a').each(function() {
									var label = $(this);
									var regex = new RegExp(autocompleteData.term, "gi");
									label.html(label.text().replace(regex, function(matched) {
										return termTemplate.replace('%s', matched);
									}));
								});
							},
							change : function(event, ui) {
								var parent = $(this).parent();
								var textValue = $.trim(this.value);
								var rmsUploadRowId = this.id.replace("txtMasterDevices", "");
								var masterDeviceId = $.trim(parent.find("input.masterDeviceId")[0].value);
								var deviceName = $.trim(parent.find("input.masterDeviceName")[0].value);

								/*
								 * Populate the text field if the user was auto completing, or clear it out if they were deleting the text
								 */
								if (textValue) {
									// If the device id is not set, then we reset to blank
									if (!masterDeviceId) {
										textValue = "";

									} else {
										// Set the name to the device name
										textValue = deviceName;

									}
									this.value = textValue;
									set_mapped(rmsUploadRowId, masterDeviceId);
								} else {
									parent.find("input.masterDeviceId")[0].value = "";
									parent.find("input.masterDeviceName")[0].value = "";
									parent.find("input.manufacturerName")[0].value = "";
									this.value = textValue;
									set_mapped(rmsUploadRowId, 0);
								}

							}
						});
					}
				},
				editurl : 'dummy.php'
			});

	jQuery("#mapped_list").jqGrid('navGrid', '#mapped_pager', {
		add : false,
		del : false,
		edit : false,
		refresh : false,
		search : false
	}, {
		closeAfterEdit : true,
		recreateForm : true,
		closeOnEscape : true,
		width : 400,
		top : sTop,
		left : sLeft
	}, {
		closeAfterAdd : true,
		recreateForm : true,
		closeOnEscape : true,
		width : 400,
		top : sTop,
		left : sLeft
	}, {}, {}, {});

	if (grid_display == 'block') {
		toggle_mapped();
	}
});

/**
 * This function handles posting data.
 * 
 * @param inAction
 *            The action to perform
 */
function doAction(inAction) {
	if (inAction == 'cancel') {
		document.location.href = TMTW_BASEURL + "data";
	}
}

/**
 * TODO: Write function description
 * 
 * @param id
 * @param key
 */
function add_device(id, key) {
	document.getElementById("hdnID").value = id;
	document.getElementById("hdnGrid").value = document.getElementById("mapped_list_container").style.display;
	document.getElementById("mapping").action = TMTW_BASEURL + "data/adddevice";
	document.getElementById("mapping").submit();
}

/**
 * Sets a device mapping to a new master device
 * 
 * @param rmsUploadRowId
 *            The upload row id
 * @param masterDeviceId
 *            The master device id. Can be 0 or false
 */
function set_mapped(rmsUploadRowId, masterDeviceId) {
	$.ajax({
		type : "GET",
		contentType : "application/json; charset=utf-8",
		url : TMTW_BASEURL + 'data/setmappedto',
		data : {
			"masterDeviceId" : masterDeviceId,
			"rmsUploadRowId" : rmsUploadRowId
		},
		error : function() {
			$('#message_container').html("Error setting mapped device!");
		}
	});
}

/**
 * Removes an unknown device instance record
 * 
 * @param id
 */
function remove_device(id) {
	// if(confirm("Are you sure you want to remove this device?")) {
	url = TMTW_BASEURL + 'data/removedevice?key=' + id;
	$.ajax({
		type : "POST",
		contentType : "application/json; charset=utf-8",
		url : url,
		error : function() {
			$('#message_container').html("Error removing device!");
		}
	});

	// refresh the grids
	setTimeout("$('#results_list').trigger('reloadGrid')", 1000);
	setTimeout("$('#mapped_list').trigger('reloadGrid')", 1000);
	// }
}

/**
 * Toggles the visibility of the mapped printers grid
 */
function toggle_mapped() {
	if (document.getElementById('mapped_list_container').style.display == 'none') {
		if (show_mapped == false) {
			$('#mapped_list').setGridParam({
				url : TMTW_BASEURL + 'data/mastermappinglist'
			});
			setTimeout("$('#mapped_list').trigger('reloadGrid')", 1000);
			show_mapped = true;
		}
		$("#toggle_mapped_link").html("[-] Hide Mapped Printers");
		document.getElementById('mapped_list_container').style.display = 'block';
		grid_display = '';
	} else {
		$("#toggle_mapped_link").html("[+] Show Mapped Printers");
		document.getElementById('mapped_list_container').style.display = 'none';
	}
}