<?php
/**
 * RetrieveDataModel.php
 *
 * Author: Matthew
 * Date: 17/01/2021
 *
 * @author Matthew
 */

namespace Meeting;

class RetrieveDataModel
{
    private $database_handle;
    private $soap_client_handle;
    private $downloaded_data;
    private $database_connection_messages;

    public function __construct()
    {
        $this->database_handle = null;
        $this->soap_client_handle = null;
        $this->downloaded_data = array();
        $this->database_connection_messages = array();
    }

    public function __destruct()
    {
    }

    public function set_database_handle($p_obj_database_handle)
    {
        $this->database_handle = $p_obj_database_handle;
    }

    public function do_get_database_connection_result()
    {
        $this->database_connection_messages = $this->database_handle->get_connection_messages();
    }

    public function do_download_data()
    {
        $this->do_create_soap_client();
        $this->getData();

        if ($this->$downloaded_data['soap-server-get-quote-result']) {
            $this->parseDownloadedData();
        }
    }

    public function get_downloaded_stock_data_result()
    {
        return $this->$downloaded_data;
    }

    private function parseDownloadedData()
    {
        $this->$downloaded_data['downloaded-company-data'] = DataContainer::make_sq_xml_parser_wrapper($this->$downloaded_data['raw-xml']);
    }

    public function storeDownloadedStockData()
    {
        if ($this->$downloaded_data['soap-server-get-quote-result']) {
            if ($this->$downloaded_data['stock-data-available']) {
                $this->prepareStockData();
                if (!$this->doesCompanyExist()) {
                    $this->storeNewCompanyDetails();
                }

                if (!$this->checkIfDataPreStored()) {
                    $this->storeNewData();
                }
            }
        }
    }

    private function prepareStockData()
    {
        $database_connection_error = $this->database_connection_messages['database-connection-error'];

        if (!$database_connection_error) {
            $stock_date = $this->$downloaded_data['downloaded-company-data']['DATE'];
            $stock_time = $this->$downloaded_data['downloaded-company-data']['TIME'];

            $arr_date = explode('/', $stock_date);
            if (sizeof($arr_date) == 3) {
                $arr_prepared_quote_details['stock-date'] = $arr_date[2] . '-' . $arr_date[0] . '-' . $arr_date[1];
            } else {
                $arr_prepared_quote_details['stock-date'] = $stock_date;
            }

            $arr_time = explode(':', $stock_time);
            if (sizeof($arr_time) == 3) {
                $arr_prepared_quote_details['stock-time'] = $arr_time[0] . ':' . intval($arr_time[1]) . ':00';
            } else {
                $arr_prepared_quote_details['stock-time'] = $stock_time;
            }

            $this->downloaded_stockquote_data = array_merge($this->downloaded_stockquote_data, $arr_prepared_quote_details);
        }
    }

    /** example of web service API update
     * time now has am/pm appended
     * so these now have to be stripped out
     * so that the database will accept this data
     */
    private function reformatTimeString()
    {
        $stock_time = $this->downloaded_stockquote_data['stock-time'];
        $stock_time = str_replace('am', '', $stock_time);
        $stock_time = str_replace('pm', '', $stock_time);
        $this->$downloaded_data['stock-time'] = $stock_time;
    }
}

