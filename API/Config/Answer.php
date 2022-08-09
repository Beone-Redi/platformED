<?php

class Answer
{
  public function response($respuesta,$peticion)
  {

     
      $IdTransactions=date('YmdHis');
        try
        {
     
          $respuesta_name=array_keys($respuesta);
          $peticion_name=array_keys($peticion);
          $string1=$string2='';
          for ($i=0; $i < Sizeof($respuesta_name); $i++) { 
              $string1.="[".$respuesta_name[$i]."]=>".$respuesta[$respuesta_name[$i]]." ";
          }


          for ($i=0; $i < Sizeof($peticion_name); $i++) { 
            $string2.="[".$peticion_name[$i]."]=>".$peticion[$peticion_name[$i]]." ";
          }
          $string2 = substr($string2, 0, -1);  // returns "abcde"
          $string1 = substr($string1, 0, -1);  // returns "abcde"

          $log=[$IdTransactions,
          $string2,
          $string1]; 

          if(isset($respuesta['ErrorMesage']))
          {
            $fichero = fopen('Logs/log_errors.csv', 'a' );
          }
          else
          {
            $fichero = fopen('Logs/log_respuestas.csv', 'a' );
          }
          fputcsv($fichero,$log );
          fclose($fichero);
    
            echo json_encode($respuesta);
            exit();
        }
        catch ( Exception $ERROR )
        {
            $CMF['Transactions']=$IdTransactions;
            $CMF['ErrorMesage']='51'.$ERROR->getMessage();
            echo json_encode($CMF);
            exit();
        }
    }
}
