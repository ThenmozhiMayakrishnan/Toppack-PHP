<?php
namespace Handlers;

class DataHandler {
    public function getImportedRepositories($pdo) {
      $getReposQuery = $pdo->prepare("SELECT * FROM repository");
      try {
        $getReposQuery->execute();
        $repos = $getReposQuery->fetchAll();
        $json = json_encode($repos);
        return $json;
      } catch(\PDOException $e) {
        return array("errorMessage" => $e->getMessage());
      }
    }

    public function importRepository($data, $pdo){
        $insertRepoQuery = $pdo->prepare("INSERT INTO repository (name, description, url, forks_count, stars_count, watchers_count, author, profile_pic_url) values (?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $insertRepoQuery->execute([$data["name"], $data["description"], $data["url"], $data["forks_count"], $data["stars_count"], $data["watchers_count"], $data["author"], $data["profile_pic_url"]]);
            $repoId = $pdo->lastInsertId();
            foreach($data["dependencies"] as $dep){
              $fetchPackageQuery = $pdo->prepare("SELECT * FROM Package where name='".$dep."' LIMIT 1");
              $fetchPackageQuery->execute();

              $package = $fetchPackageQuery->fetch();
              if (!$package){
                $insertPackageQuery = $pdo->prepare("INSERT INTO Package (name) values (?)");
                $insertPackageQuery->execute([$dep]);
                $packageId = $pdo->lastInsertId();
              }
              else{
                $packageId = $package["package_id"];
              }
              $insertRepoPackageQuery = $pdo->prepare("INSERT INTO repository_packages (repo_id, package_id)
                                                      values (?, ?)");
              $insertRepoPackageQuery->execute([$repoId, $packageId]);
            }
            return array("success" => true, "packages" => $data["dependencies"]);
        } catch (\PDOException $e) {
            return array("error" => true, "sql_error" => $e->getMessage(), "message" => "This Repo is already imported.");
        }
    }

    public function checkIfRepoImported($url, $pdo){
      $checkIfRepoExistsQuery = $pdo->prepare("SELECT * FROM Repo where url='".$url."' LIMIT 1");
      try{
        $checkIfRepoExistsQuery->execute();
        $repo = $checkIfRepoExistsQuery->fetch();
        if (!$repo){
          return false;
        }else{
          return true;
        }
      } catch (\PDOException $e){
        return false;
      }
    }

    public function getTopPackages($pdo){
      $fetchMostUsedPackageNames = $pdo->prepare("SELECT p.name, p.package_id, count(p.name) FROM package p RIGHT JOIN repositorypackages rp on p.package_id = rp.package_id RIGHT JOIN repo r on rp.repo_id = r.repo_id GROUP BY p.name, p.package_id ORDER BY COUNT(p.name) DESC LIMIT 10;");
      try{
        $data = [];
        $fetchMostUsedPackageNames->execute();
        $packages = $fetchMostUsedPackageNames->fetchAll();
        if ($packages){
          foreach($packages as $p){
            $name = $p['name'];
            $package_id = $p['package_id'];
            $count = $p['count(p.name)'];
            $fetchRepoData = $pdo->prepare("SELECT r.name, r.owner, r.url FROM repo r RIGHT JOIN repository_packages rp on r.repo_id = rp.repo_id RIGHT JOIN package p ON rp.package_id = p.package_id WHERE p.package_id=?");
            $data[$name] = ["count" => $count, "repos" => []];
            try{
              $fetchRepoData->execute([$package_id]);
              $repos = $fetchRepoData->fetchAll();
              foreach($repos as $repo){
                $repo_array = ["name" => $repo["name"], "owner" => $repo["owner"], "url" => $repo["url"]];
                array_push($data[$name]["repos"], $repo_array);
              }
            }catch(\PDOException $e){
              return array("error" => true, "sql_error" => $e->getMessage(), "message" => "There was an error connecting to the database.");
            }
          }
          return $data;
        }else{
          return array("error" => true, "message" => "No packages imported yet.");
        }
      } catch (\PDOException $e){
        return array("error" => true, "sql_error" => $e->getMessage(), "message" => "There was an error connecting to the database.");      }
    }
}
