package main

import (
	"fmt"
	"io"
	"os"
	//"net"
	"net/http"
	//"time"
	"archive/zip"
    "path/filepath"
    //"strings"
    "github.com/urfave/cli"
)

// http://192.168.33.21/src/packager.php?vagrant=basic-ubuntu&provision=ansible/basic-lamp&editorconfig=

func main() {
  var vagrant string
  var provision string
  var editorconfig string

  app := cli.NewApp()

  app.Flags = []cli.Flag{
    cli.StringFlag{
      Name:  "vagrant",
      Usage: "The Vagrant machine to use",
      Destination: &vagrant,
    },
    cli.StringFlag{
      Name:  "provision",
      Usage: "The provisioner to use. TOOL/CONFIG, e.g. ansible/lamp",
      Destination: &provision,
    },
    cli.StringFlag{
      Name:  "editorconfig",
      Usage: "Whether to include the .editorconfig or not",
      Destination: &editorconfig,
    },
  }

  app.Action = func(c *cli.Context) error {
    var url string;
    url = fmt.Sprintf("http://192.168.33.21/src/packager.php?vagrant=%s&provision=%s&editorconfig=%s", vagrant, provision, editorconfig)
    fmt.Println(url)

    response, err := http.Get(url)
        if err != nil {
                //log.Fatal(err)
        } else {
                defer response.Body.Close()
                out, err := os.Create("filename.zip")
                  if err != nil {
                    // panic?
                  }
                  defer out.Close()
                  io.Copy(out, response.Body)

                  status := Unzip("filename.zip", "tmp")
                  fmt.Println(status)
        }

    return nil
  }

  app.Run(os.Args)


}

func Unzip(src, dest string) error {
    r, err := zip.OpenReader(src)
    if err != nil {
        return err
    }
    defer func() {
        if err := r.Close(); err != nil {
            panic(err)
        }
    }()

    os.MkdirAll(dest, 0755)

    // Closure to address file descriptors issue with all the deferred .Close() methods
    extractAndWriteFile := func(f *zip.File) error {
        rc, err := f.Open()
        if err != nil {
            return err
        }
        defer func() {
            if err := rc.Close(); err != nil {
                panic(err)
            }
        }()

        path := filepath.Join(dest, f.Name)

        if f.FileInfo().IsDir() {
            os.MkdirAll(path, f.Mode())
        } else {
            os.MkdirAll(filepath.Dir(path), f.Mode())
            f, err := os.OpenFile(path, os.O_WRONLY|os.O_CREATE|os.O_TRUNC, f.Mode())
            if err != nil {
                return err
            }
            defer func() {
                if err := f.Close(); err != nil {
                    panic(err)
                }
            }()

            _, err = io.Copy(f, rc)
            if err != nil {
                return err
            }
        }
        return nil
    }

    for _, f := range r.File {
        err := extractAndWriteFile(f)
        if err != nil {
            return err
        }
    }

    return nil
}
