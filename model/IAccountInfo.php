<?php

namespace Login\model;

/**
 * This interface provides a simplified access point to an AccountRegister.
 */
interface IAccountInfo {

  function isAccountCreated(string $username) : bool;

  function getAccountByCredentials(AccountCredentials $credentials) : Account;
  function getAccountByUsername(string $username) : Account;
  function getAccountById(string $id) : Account;
  function getAccounts() : array;
}