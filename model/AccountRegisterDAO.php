<?php

namespace Login\model;

interface AccountRegisterDAO {

  function isAccountCreated(string $username) : bool;

  function createAccount(\Login\model\Username $username, \Login\model\Password $password);
  function updateAccount(Account $account, array $updates) : Account;
  function deleteAccount(Account $account);

  function getAccountByCredentials(AccountCredentials $credentials) : Account;
  function getAccountByUsername(string $username) : Account;
  function getAccountById(string $id) : Account;
  function getAccounts() : array;
}