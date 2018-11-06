import 'package:flutter/material.dart';
import 'dart:async';
import 'CNetworking.dart';

  //debe ser global.
  CNetworking network = new CNetworking(
    apiPath: "http://186.159.129.2/isaac/API3/",
    apiKey: "sdhvY6232GBE3JH@sj2",
  );

Future getCervezasMap(BuildContext c) async {
  var map = {"statement": "SELECT * FROM cervecero_cervezas"};

  return await network.post(map).then((result) {
    if (result == null || !network.isSuccess()) {
      print("Error estableciendo conexión al servidor.");
      return null;
    }
    return network.getAllData();
  });
}
