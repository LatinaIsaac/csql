import 'package:flutter/material.dart';
import 'CSql.dart';

void main() => runApp(new MyApp());

class MyApp extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return new MaterialApp(
      title: 'Flutter Demo',
      home: new MyHomePage(title: 'Flutter Demo Home Page'),
    );
  }
}

class MyHomePage extends StatefulWidget {
  MyHomePage({Key key, this.title}) : super(key: key);

  final String title;

  @override
  _MyHomePageState createState() => new _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage> {
  final CSql sql = new CSql();
  @override
  Widget build(BuildContext context) {
    futureTest();
    return new Center();
  }

  void futureTest() async {
    CSql result =  sql
      ..SELECT("*")
      ..FROM("cervecero_cervezas")
      ..WHERE("id_cerveza >= 2")
      ..EXECUTE();

    print("------------------------------------------ SQL METHODS ------------------------------------------");
    print("getStatusMessage: " + result.getStatusMessage());
    print("getStatus: " + result.getStatus().toString());
    print("fetchRow: " + result.fetchRow("nombre"));
    print("fetchAssoc: " + result.fetchAssoc().toString());
    print("---------------------------------------- END SQL METHODS ----------------------------------------");

    //podemos inclusive trabajar como PHP fetchAssoc()
    result.fetchAssoc().forEach((row) {
      //print(row["nombre"]);
      row.forEach((k,v){
        print("$k => $v");
      });
    });

  }
}
