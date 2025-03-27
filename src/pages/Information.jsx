import React, { useEffect, useState } from "react";
import { Domain } from "../config/Api";
import axios from "axios";
import { Button, Table } from "react-bootstrap";

const Information = () => {
  const [name, setName] = useState("");
  const [accountNo, setAccountNo] = useState("");
  const [preferenceNo, setPreferenceNo] = useState("");

  const [showTable, setShowTable] = useState(false);
  const [accountInfos, setAccountInfos] = useState([]);

  useEffect(() => {
    getInfo();
  }, []);

  const getInfo = async () => {
    const res = await axios.post(`${Domain}/api/balance-inquiry`);
    console.log(res.data.data.accountInfos);
    setName(res.data.data.name);
    setAccountNo(res.data.data.accountNo);
    setPreferenceNo(res.data.data.partnerReferenceNo);
    setAccountInfos(res.data.data.accountInfos || []);
  };

  // Fungsi untuk mengubah camelCase menjadi format yang lebih rapi
  const formatFieldName = (field) => {
    return field
      .replace(/([a-z])([A-Z])/g, "$1 $2") // Tambah spasi antara huruf kecil dan besar
      .replace(/^./, (str) => str.toUpperCase()); // Kapitalisasi huruf pertama
  };

  return (
    <div className="container">
      <div className="mt-3 text-center">
        <h2>Informasi Saldo</h2>
        <h6>Nama : {name}</h6>
        <h6>Account No : {accountNo}</h6>
        <h6>Partner Reference No : {preferenceNo}</h6>
        {/* <Button variant="btn btn-primary mt-2">Check Saldo</Button> */}
        <Button
          variant="primary"
          onClick={() => setShowTable(true)}
          className="mt-3"
        >
          Check Saldo
        </Button>
        <hr />
        {showTable &&
          accountInfos.map((balance, index) => (
            <div key={index} className="mt-4">
              <p className="text-start mb-1">
                <strong>Balance Type : {balance.balanceType} </strong>
              </p>
              <p className="text-start mb-1">
                <strong>Status : {balance.status} </strong>
              </p>
              <p className="text-start mb-1">
                <strong>
                  Reg Status Code : {balance.registrationStatusCode}
                </strong>
              </p>
              <Table striped bordered hover className="text-start mt-2">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Account Infos</th>
                    <th>Value</th>
                    <th>Currency</th>
                  </tr>
                </thead>
                <tbody>
                  {Object.entries(balance).map(([key, value], i) => {
                    // Pastikan hanya data yang memiliki value object yang diproses
                    if (
                      typeof value === "object" &&
                      value !== null &&
                      "value" in value
                    ) {
                      return (
                        <tr key={i}>
                          <td>{i}</td>
                          <td>{formatFieldName(key)}</td>
                          <td>{value.value}</td>
                          <td>{value.currency}</td>
                        </tr>
                      );
                    }
                    return null;
                  })}
                </tbody>
              </Table>
            </div>
          ))}

        {/* {showTable && (
          <div className="mt-4">
            <p
              style={{ fontSize: "13px", fontWeight: "500" }}
              className="text-start mb-1"
            >
              Balance Type: {balanceType}
            </p>
            <p
              style={{ fontSize: "13px", fontWeight: "500" }}
              className="text-start mb-1"
            >
              Status: {status}
            </p>
            <p
              style={{ fontSize: "13px", fontWeight: "500" }}
              className="text-start mb-0"
            >
              Reg Status Code: {statusCode}
            </p>
            <Table striped bordered hover className="mt-2">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Account Infos</th>
                  <th>Value</th>
                  <th>Currency</th>
                </tr>
              </thead>
              <tbody>
                {balance.details.map((item, i) => (
                  <tr key={i}>
                    <td>{i + 1}</td>
                    <td>{item.info}</td>
                    <td>{item.value}</td>
                    <td>{item.currency}</td>
                  </tr>
                ))}
              </tbody>
            </Table>
          </div>
        )} */}
      </div>
    </div>
  );
};

export default Information;
