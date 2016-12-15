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

// Uses Go CLI package - please see here for documentation: https://github.com/urfave/cli

func main() {
    // set some variables
    var vagrant string
    var provision string
    var editorconfig bool

    // read in the source server (i.e. where to get packages from). Expect to be in an environment variable
    var sourceServer string
    sourceServer = os.Getenv("SHOELACE_SERVER")

    // if we don't have a server then error and bail
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
            // define flags that the "init" command can use. E.g. shoelace init --flag1=foo --flag2=bar
            Flags: []cli.Flag{
                cli.StringFlag{
                    Name: "vagrant",
                    Usage: "The Vagrant machine to use",
                    Destination: &vagrant,  // the variable to pop this value in
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

                // build the URL to the package server and pass arguments
                url = fmt.Sprintf("%spackager.php?vagrant=%s&provision=%s&editorconfig=%t", sourceServer, vagrant, provision, editorconfig)
                fmt.Println(url)

                // make the HTTP request to the URL (just an HTTP GET request)
                response, err := http.Get(url)

                if err != nil {
                    // if there was an error handle it (or not)
                } else {
                    // once we're done with the response body close it
                    defer response.Body.Close()

                    // create a file to store the response from the package server
                    out, err := os.Create("filename.zip")
                    if err != nil {
                        // handle file creation error
                        // panic?
                    }

                    // defer closing the output file until the function we're in has completed
                    defer out.Close()

                    // set the content of the output file with the response from the package server
                    io.Copy(out, response.Body)

                    // unzip the file we got into the current directory
                    status := Unzip("filename.zip", "")
                }

                return nil
            },
        },
    }

    // when the command(s) above have completed, remove the downloaded package from the client
    defer func () {
        os.Remove("filename.zip");
    }()

    // run the CLI app
    app.Run(os.Args)
}

/*
    Unzip the file in src to dest
    @see: http://stackoverflow.com/questions/20357223/easy-way-to-unzip-file-with-golang
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
