import {API_URL} from '../../../config.js';
import { defaultHeaders } from './apiClient.js';

export function initDataTable(selector,endpoint,columns) {

    return $(selector).DataTable({
        ajax: {
            url: `${API_URL + endpoint}`,
            type: 'GET',
            headers: defaultHeaders
        },
        dataSrc: function (json) {
            console.log("Data Table: ",json);
            if(!json.success) {
                SwalPopup("Error", json.message || "Failed to load data", "error");
                return [];
            }
            return json.data || [];
        },
        error: function(xhr,error,thrown) {
            console.error(`Error fetching data: ${error}`, thrown);
        SwalPopup("Error", "Failed to fetch data from the server", "error");
        },
        columns: columns,
        dom: 'lBfrtip', 
        buttons: [

            {
                extend: "csvHtml5",
                className: "btn rounded m-1",
                text: `<i class="fas fa-file-csv"></i> CSV`
            },
            {
                extend: "excelHtml5",
                className: "btn btn-success rounded m-1",
                text: `<i class="fas fa-file-excel"></i> Excel`
            },
            {
                extend: "print",
                className: "btn btn-info rounded m-1 text-white",
                text: `<i class="fas fa-print"></i> Table`
            },
            {
                extend: "pdf",
                className: "btn btn-danger rounded m-1",
                text: `<i class="fas fa-file-pdf"></i> PDF`
            }
        ],
        searching: true,
        responsive: true,
        pagination: true,
    })
}