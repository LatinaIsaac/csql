import 'CNetworking.dart';

class CSql {

  String sqlBuilder;
  List params;
  CNetworking network;

  CSql() {
    network = new CNetworking(
      apiPath: "http://186.159.129.2/isaac/API3/",
      apiKey: "sdhvY6232GBE3JH@sj2",
    );
  }

  /// Lun. 5 nov. [Isaac]
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

  void FROM (String from) 
  {
    if (from.contains("FROM"))
      print("[CRITICAL ERROR]: no need of FROM");
    else   
      this.sqlBuilder = "FROM $from"; 
  }

  void WHERE (String where) 
  {
    if (where.contains("FROM"))
      print("[CRITICAL ERROR]: no need of WHERE");
    else   
      this.sqlBuilder = "FROM $where"; 
  }

  dynamic EXECUTE () async
  {
    var map = {
      "statement": this.sqlBuilder,
      "p": this.params,
    };

    return await this.network.post(map).then((result) {
      if (result == null || !network.isSuccess()) {
        print("Error estableciendo conexión al servidor.");
        return null;
      }
      return network.getAllData();
    });    
  }

}