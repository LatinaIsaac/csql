import 'CNetworking.dart';

class CSql {

  String sqlBuilder;
  String params = "";
  CNetworking network;

  /// toda sentencia construida (SQLBUILDER) deberá si o sí terminar con un  EXECUTE
  /// el cual será el encargado de conectar con CNetworking para hacer la petición WEB.
  /// luego de esto la info puede ser usada por medio de las funciones getStatusMessage
  /// fetchAssoc, fetchRow
  ///    CSql result =  sql
  ///    ..SELECT("*")
  ///    ..FROM("cervecero_cervezas")
  ///    ..INNERJOIN("cervezero_miscervezas", "cervezero_miscervezas.id = cervecero_cervezas.id")
  ///    ..WHERE("id_cerveza = 1")
  ///    ..EXECUTE();
  CSql() {
    network = new CNetworking(
      apiPath: "http://186.159.129.2/isaac/API3/",
      apiKey: "sdhvY6232GBE3JH@sj2",
    );
  }

  /// Lun. 5 nov. 2018 [Isaac]
  /// SELECT Function
  /// sql.SELECT ("column1, column2, column3");
  /// @return void
  void SELECT(String columns) 
  {
    if (columns.contains("SELECT"))
      print("[CRITICAL ERROR]: no need of SELECT");
    else
      this.sqlBuilder = "SELECT $columns ";
  }

  void INNERJOIN (String inner, String onclause) 
  {
    if (inner.contains("INNER JOIN"))
      print("[CRITICAL ERROR]: no need of INNER JOIN");
    else   
      this.sqlBuilder += "INNER JOIN $inner ON  $onclause "; 
  }

  void LEFTJOIN (String left, String onclause) 
  {
    if (left.contains("LEFT JOIN"))
      print("[CRITICAL ERROR]: no need of LEFT JOIN");
    else   
      this.sqlBuilder += "LEFT JOIN $left ON  $onclause "; 
  }

  /// Lun. 5 nov. 2018 [Isaac]
  /// SELECT Function
  /// sql
  ///   ..SELECT ("column1, column2, column3")
  ///   ..FROM ("table")
  /// @return void
  void FROM (String from) 
  {
    if (from.contains("FROM"))
      print("[CRITICAL ERROR]: no need of FROM");
    else   
      this.sqlBuilder += "FROM $from "; 
  }

  /// Lun. 5 nov. 2018 [Isaac]
  /// SELECT Function
  /// sql
  ///   ..SELECT ("column1, column2, column3")
  ///   ..FROM ("table")
  ///   ..WHERE("? = ?")
  /// @return void
  void WHERE (String where) 
  {
    if (where.contains("FROM"))
      print("[CRITICAL ERROR]: no need of WHERE");
    else   
      this.sqlBuilder += "WHERE $where"; 
  }

  /// Lun. 5 nov. 2018 [Isaac]
  /// UPDATE function
  /// sql..UPDATE("table")
  /// @return void
  void UPDATE (String update) 
  {
    if (update.contains("UPDATE"))
      print("[CRITICAL ERROR]: no need of UPDATE");
    else   
      this.sqlBuilder += "Update $update "; 
  }

  /// Lun. 5 nov. 2018 [Isaac]
  /// UPDATE function
  /// sql
  ///   ..UPDATE("table")
  ///   ..SET(?=1, nombre="Isaac")
  /// @return void
  void SET (String setter) 
  {
    this.sqlBuilder += "SET $setter"; 
  }


  /// Lun. 5 nov. 2018 [Isaac]
  /// DELETE function
  /// sql
  ///   ..DELETE("table")
  ///   ..WHERE("...")
  void DELETE (String table) 
  {
    this.sqlBuilder += "DELETE FROM ";
  }


  /// Lun. 5 nov. 2018 [Isaac]
  /// EXECUTE function
  /// toda sentencia construida (SQLBUILDER) deberá si o sí terminar con un  EXECUTE
  /// el cual será el encargado de conectar con CNetworking para hacer la petición WEB.
  /// luego de esto la info puede ser usada por medio de las funciones getStatusMessage
  /// fetchAssoc, fetchRow
  ///    CSql result =  sql
  ///    ..SELECT("*")
  ///    ..FROM("cervecero_cervezas")
  ///    ..INNERJOIN("cervezero_miscervezas", "cervezero_miscervezas.id = cervecero_cervezas.id")
  ///    ..WHERE("id_cerveza = 1")
  ///    ..EXECUTE();
  /// sql
  Future EXECUTE () async
  {
    var map = {
      "statement": this.sqlBuilder,
    };

    return await this.network.post(map).then((result) {
      if (result == null || !network.isSuccess()) {
        print("Error estableciendo conexión al servidor.");
        return false;
      }
      return true;
    });    
  }

  String getStatusMessage() {
    return this.network.getStatusMessage();
  }

  int getStatus() {
    return this.network.getStatus();
  }

  List<Map<String, dynamic>> fetchAssoc() {
    return this.network.getAllData();
  }

  dynamic fetchRow(String field) {
    return this.network.getSingleData(field);
  }

  

}