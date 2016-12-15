package main

import (
    "fmt"
    "io"
    "os"
    "net/http"
    "archive/zip"
    "path/filepath"
    "github.com/urfave/cli"
)

func main() {
    // set some variables
    var vagrant string
    var provision string
    var editorconfig bool

    var sourceServer string
    sourceServer = os.Getenv("SHOELACE_SERVER")

    fmt.Println(sourceServer)

    if sourceServer == "" {
        fmt.Println("No SHOELACE_SERVER environment variable found, please add one and run shoelace again")
        return
    }


    // create a new CLI app
    app := cli.NewApp()

    // add command(s) to the CLI app
    app.Commands = []cli.Command{
        {
            Name: "init",
            Usage: "Initialise a project with given settings",
            // define flags that the "init" command can use
            Flags: []cli.Flag{
                cli.StringFlag{
                    Name: "vagrant",
                    Usage: "The Vagrant machine to use",
                    Destination: &vagrant,
                },
                cli.StringFlag{
                    Name: "provision",
                    Usage: "The provisioner to use. TOOL/CONFIG, e.g. ansible/lamp",
                    Destination: &provision,
                },
                cli.BoolFlag{
                    Name:  "editorconfig",
                    Usage: "Whether to include the .editorconfig or not",
                    Destination: &editorconfig,
                },
            },
            // define tha actual work to do when "init" is used
            Action:  func(c *cli.Context) error {
                var url string;
                url = fmt.Sprintf("%spackager.php?vagrant=%s&provision=%s&editorconfig=%t", sourceServer, vagrant, provision, editorconfig)
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

                    status := Unzip("filename.zip", "")
                    fmt.Println(status)
                }

                return nil
            },
        },
    }

    defer func () {
        os.Remove("filename.zip");
    }()

    app.Run(os.Args)
}

/*

 */
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
