<?php

class chunkReadFilter  implements \PHPExcel_Reader_IReadFilter
{
	private $_startRow = 0;
	private $_endRow = 0;
	/**  Set the list of rows that we want to read  */
	public function  setRows ($startRow, $chunkSize) {
		$this->_startRow    = $startRow;
		$this->_endRow      = $startRow + $chunkSize;
	}
	public function readCell($column, $row, $worksheetName = '') {
		//  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
		if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
			return true;
		}
		return false;
	}
}


class phpExcel_optim{
	# chunkSize размер считываемых строк за раз ,
	# чем больше тем быстрей выполняется , но требует больше оперативы ,
	# если меньше соответственно все наоборот
	public $chunkSize = 10000;
	private $startRow;	//начинаем читать со строки 2, в PHPExcel первая строка имеет индекс 1, и как правило это строка заголовков
	private $file;
	private $exit = false; //флаг выхода
	private $empty_value = 0;		//счетчик пустых знаений
	private $type_file; // тип файла  Excel5 или Excel2007
	private $tmp_path; // путь где размешаются временные файлы

	private $tmp_files = array();

	public function __construct($file, $tmp_path , $startRow = 1){
		$this->file = $file;
		$this->startRow = $startRow;

		$this->tmp_path = $tmp_path ;

		$ext = (string)substr($this->file,$pos=strrpos($this->file,'.')+1);

		$this->type_file = ($ext == "xls") ? 'Excel5' : 'Excel2007';

	}

	/*
	 * Create temp file/Создание временного файла
	 * example:
	 * <?php
	 * 	return array(...);
	 */
	function tmp_file_write( & $data ){

		$file_name = $this->tmp_path. 'phpExcel_' . md5(uniqid("")) . '.php'; // random file name

		if( ! $file_stream = fopen( $file_name , "a+bt") ){
			$this->tmp_files_delete();
			throw new Exception('Ошибка доступа к файлу/каталогу (запись): '.$file_name);
		}

		$text = "<?php\n return " . var_export( $data, 1 ) . ";";
		if ( fwrite( $file_stream , $text) === FALSE) {
			$this->tmp_files_delete();
			throw new Exception('Ошибка записи данных в файл');
		}

		fclose( $file_stream );

		$this->tmp_files[] = $file_name; // add file name in array, for deletion

		$data = array(); // clear memory

	}

	// marge data from temp files / обьеденяем данные из временных файлов
	private function & get_data_in_tmp_files(){
		$data = array();

		foreach( $this->tmp_files as $file_name ){
			$data += include_once $file_name;
		}

		$this->tmp_files_delete();

		return $data;
	}

	// delete temp files / удаление временных файлов
	private function tmp_files_delete(){
		foreach($this->tmp_files as $key=>$file_name){
			unlink($file_name);
			unset($this->tmp_files[$key]);
		}
	}

	public function __destruct(){
		$this->tmp_files_delete();
	}


	public function read(){
		$objReader = \PHPExcel_IOFactory::createReader($this->type_file);

		// $objReader->setReadDataOnly(true);

		$chunkFilter = new  chunkReadFilter ();
		$objReader->setReadFilter($chunkFilter);
		$objReader->setReadDataOnly(true);
		//внешний цикл, пока файл не кончится


		$return_row = array();
		$sheet_num = 0;

		$startRow = $this->startRow;
		$metrik_row = 0; // общий счетчик строк, по всем листам
		while ( !$this->exit )
		{
			$sheet_next_check = false;
			$chunkFilter-> setRows ($startRow,$this->chunkSize); 	//устанавливаем знаечние фильтра
			$objPHPExcel = $objReader->load($this->file);		//открываем файл

			if(! isset($sheetCount) )
				$sheetCount = $objPHPExcel -> getSheetCount() - 1 ; // колисество листов

			$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet_num);		//устанавливаем индекс активной страницы
			$nColumn = \PHPExcel_Cell::columnIndexFromString(
				$objWorksheet->getHighestColumn()
			);

			for ($i = $startRow; $i < $startRow + $this->chunkSize; $i++) 	//внутренний цикл по строкам
			{
				$value = trim(htmlspecialchars($objWorksheet->getCellByColumnAndRow(0, $i)->getValue()));		//получаем первое знаение в строке

				$empty = empty($value);
				if ( $empty )		//проверяем значение на пустоту
					$this->empty_value++;
				if ($this->empty_value == 3) //после трех пустых значений
				{
					if($sheetCount === $sheet_num){
						$this->exit = true; // завершаем обработку , думая, что это конец
					}else{
						$sheet_next_check = true; // отмечаем что необходимо переключить цикл на следующий лист
					}

					break;
				}

				if( ! $empty ){
					for ($j = 0; $j < $nColumn; $j++) {
						$return_row[$metrik_row][$j] = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
					}
					$metrik_row++;
				}

			}

			$objPHPExcel->disconnectWorksheets(); 				//чистим
			unset($objPHPExcel); 						//память

			if( $sheet_next_check ){ // если переключаемся на новый лист
				$sheet_num++; // сдвигаем индекс листа
				$this->empty_value = 0; // сбрасываем счетчик пустых строк
				$startRow = $this->startRow; // сбрасываем строку с который будем читать
			}else{
				$startRow += $this->chunkSize;	//переходим на следующий шаг цикла, увеличивая строку, с которой будем читать файл
			}

			$this->tmp_file_write($return_row); // записываем данные во временный файл, для освобождения оперативки
		}

		return $this->get_data_in_tmp_files(); // читаем и возвращаем данные из временных файлов
	}
}