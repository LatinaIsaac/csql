import 'package:path/path.dart';
import 'package:async/async.dart';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:async';
import 'package:flutter/material.dart';

class CNetworking {
  final String apiPath;
  final String apiKey;
  Map<String, dynamic> _map;

  CNetworking({
    @required this.apiPath,
    @required this.apiKey,
  });

  /// [ISAAC: 29/06/2018] API3 NETWORKING WORKING METHODS
  /// 22/08/2018 UPDATE API3
  /// 190 - invalid session
  /// 191 - error
  /// 191 - error
  ///
  /// 200 - select
  /// 201 - no rows founded
  /// 202 - success
  ///
  /// 300 - update or insert or delet (uid)
  /// 301 - No object founded for update
  /// 302 - success

  get getApiPath => this.apiPath;
  get getMap => this._map;
  /*
    entrada:
      var _map = {
        
        "statement":"SELECT user_id FROM cervecero_login WHERE username=? AND passw=?",
        "p": userController.text + "," + passwController.text
      };

    salida:
      flutter: {status: 202, status_message: success, columnas: 1, data: [{user_id: 2}]}
    éxito.
  */

  /// Retornará true si el código es 202 o 302. de lo contrario false
  bool isSuccess() {
    return (this.getStatus() == 202 || this.getStatus() == 302) ? true : false;
  }

  upload(File imageFile, String filename) async {
    var stream =
        new http.ByteStream(DelegatingStream.typed(imageFile.openRead()));
    var length = await imageFile.length();
    var uri = Uri.parse(this.apiPath);
    var request = new http.MultipartRequest("POST", uri);
    request.fields['statement'] = "z";
    var multipartFile = new http.MultipartFile('file', stream, length,
        filename: filename + extension(imageFile.path));

    request.files.add(multipartFile);
    var response = await request.send();

    response.stream.transform(utf8.decoder).listen((value) {
      //print(value);
    });
  }

  Future post(Map map) async {
    map.putIfAbsent("KEY", () => this.apiKey);

    final response = await http
        .post(this.apiPath, body: map);

    return this._map = json.decode(response.body) ?? null;
  }

/*
  I/flutter ( 3174): {
  I/flutter ( 3174):     "status": 200,
  I/flutter ( 3174):     "status_message": "success",
  I/flutter ( 3174):     "data": [
  I/flutter ( 3174):         {
  I/flutter ( 3174):             "user_id": "1",
  I/flutter ( 3174):             "username": "isaac",
  I/flutter ( 3174):             "passw": "1234",
  I/flutter ( 3174):             "email": "j-isaac10@hotmail.com",
  I/flutter ( 3174):             "acc_lvl": "100"
  I/flutter ( 3174):         }
  I/flutter ( 3174):     ]
  I/flutter ( 3174): }
  WILL RETURN 200.
*/
  int getStatus() {
    return this._map["status"];
  }

/*
  I/flutter ( 3174): {
  I/flutter ( 3174):     "status": 200,
  I/flutter ( 3174):     "status_message": "success",
  I/flutter ( 3174):     "data": [
  I/flutter ( 3174):         {
  I/flutter ( 3174):             "user_id": "1",
  I/flutter ( 3174):             "username": "isaac",
  I/flutter ( 3174):             "passw": "1234",
  I/flutter ( 3174):             "email": "j-isaac10@hotmail.com",
  I/flutter ( 3174):             "acc_lvl": "100"
  I/flutter ( 3174):         }
  I/flutter ( 3174):     ]
  I/flutter ( 3174): }
  WILL RETURN "success".
*/
  String getStatusMessage() {
    return this._map["status_message"] ?? "no message from JSon";
  }

/* retornará el número de filas que contiene una data.
  I/flutter ( 3174):     "data": [
  I/flutter ( 3174):         {
  I/flutter ( 3174):             "user_id": "1",
  I/flutter ( 3174):             "username": "isaac",
  I/flutter ( 3174):             "passw": "1234",
  I/flutter ( 3174):             "email": "j-isaac10@hotmail.com",
  I/flutter ( 3174):             "acc_lvl": "100"
  I/flutter ( 3174):         }
  I/flutter ( 3174):     ]
  en este caso retornaría 1.
*/
  int getRowCount() {
    return this._map["columnas"] ?? 0;
  }

/* retornará el número de filas que contiene una data.
  I/flutter ( 3174):     "data": [
  I/flutter ( 3174):         {
  I/flutter ( 3174):             "user_id": "1",
  I/flutter ( 3174):             "username": "isaac",
  I/flutter ( 3174):             "passw": "1234",
  I/flutter ( 3174):             "email": "j-isaac10@hotmail.com",
  I/flutter ( 3174):             "acc_lvl": "100"
  I/flutter ( 3174):         }
  I/flutter ( 3174):     ]
  getSingleData(result, "username") = devolvera isaac
  getSingleData(result, "passw") = devolvera 1234.
*/
  dynamic getSingleData(String field) {
    if (this._map["data"].isEmpty)
      return null;
    else {
      Map<String, dynamic> dataMap =
          json.decode(json.encode(this._map["data"][0]));
      return (field == "all" ? dataMap : dataMap[field]);
    }
  }

/*
  Devolverá un _mapa de _mapas donde la llave son números consecutivos...:
    {
    0: 
      {
        id_cerveza: 1, nombre: Lagarta, descpricion: Aroma crítico de maracuyá con sabor a pithaya, 
        tipo: Fruity Ale, amargor: Medio, alcohol: 4.2%, casa: Micro Brewing Company, pais: Costa Rica
      },
    1: 
      {
        id_cerveza: 2, nombre: Libertas, descpricion: Sabor sútil y suave para días calurosos, 
        tipo: Golden Ale, amargor: Medio, alcohol: 4.7%, casa: Costa Rica Craft Brewing Compa, pais: Costa Rica
      }, 
    2: ...
    print(data_map);
    print(data_map[0]); devolverá solo la llave 0.
    print(data_map[0]["nombre"]); devolverá: "Lagarta".
*/
  List<Map<String, dynamic>> getAllData() 
  {
    List<Map<String, dynamic>> dataList = new List();
    dataList.addAll(this._map["data"].cast<Map<String, dynamic>>());
    return dataList;
  }
}
