(function() {
    var fileSystem = function($http) {
        var apiUrl = 'http://localhost/redundancy/Includes/api.inc.php';
        var module = 'Kernel.FileSystemKernel';

        var post = function(method, args, makeEmptyStrings) {
            var params = {
                module: module,
                method: method
            };

            //arguments are optional
            if (args) {

                //if makeEmptyStrings is undefined or true
                //undefined fields will be defined as empty strings
                if (makeEmptyStrings || makeEmptyStrings === undefined)
                    params.args = makeEmptyStringsInArray(args);
                else
                    params.args = args;
            }
            return $http.post(apiUrl, params);
        };

        var makeEmptyStringsInArray = function(arr) {
            for (var i = 0; i < arr.length; i++)
                if (arr[i] === undefined)
                    arr[i] = '';
            return arr;
        }

        //API functions
        var getSystemDir = function(directory) {
            return post('GetSystemDir', [directory]);
        };
        var createDirectory = function(name, root, token) {
            var args = [
                name,
                root,
                token
            ];
            return post('CreateDirectory', args);
        };
        var getStorage = function(token) {
            return post('GetStorage', [token]);
        };
        var isDisplayNameAllowed = function(displayName) {
            return post('IsDisplayNameAllowed', [displayName]);
        };
        //TODO: $_FILES handling!
        var uploadFile = function(root, token) {
            var args = [
                root,
                token
            ];
            return post('UploadFile', args);
        };
        var deleteDirectory = function(name, token) {
            var args = [
                name,
                token
            ];
            return post('DeleteDirectory', args);
        };
        var deleteFile = function(absolutePath, token) {
            var args = [
                absolutePath,
                token
            ];
            return post('DeleteFile', args);
        };
        var getContent = function(absolutePath, token) {
            var args = [
                absolutePath,
                token
            ];
            return post('GetContent', args);
        };
        var refreshLastChangeDateTimeOfParent = function(entryID, token) {
            var args = [
                entryID,
                token
            ];
            return post('RefreshLastChangeDateTimeOfParent', args);
        };
        var calculateFolderSize = function(absolutePath, token) {
            var args = [
                absolutePath,
                token
            ];
            return post('CalculateFolderSize', args);
        };
        var moveEntry = function(oldAbsolutePath, newRoot, token) {
            var args = [
                oldAbsolutePath,
                newRoot,
                token
            ];
            return post('MoveEntry', args);
        };
        var copyEntry = function(oldAbsolutePath, newRoot, token) {
            var args = [
                oldAbsolutePath,
                newRoot,
                token
            ];
            return post('CopyEntry', args);
        };
        var getEntryByAbsolutePath = function(absolutePath, token) {
            var args = [
                oldAbsolutePath,
                token
            ];
            return post('GetEntryByAbsolutePath', args);
        };
        var renameEntry = function(id, newName, token) {
            var args = [
                id,
                newName,
                token
            ];
            return post('RenameEntry', args);
        };
        var isEntryExisting = function(name, root, token) {
            var args = [
                name,
                root,
                token
            ];
            return post('IsEntryExisting', args);
        };
        var getEntryById = function(id, token) {
            var args = [
                id,
                token
            ];
            return post('GetEntryById', args);
        };
        var getAbsolutePathbyId = function(id, token) {
            var args = [
                id,
                token
            ];
            return post('GetAbsolutePathById', args);
        };
        //end API functions

        return {
            getSystemDir: getSystemDir,
            createDirectory: createDirectory,
            getStorage: getStorage,
            isDisplayNameAllowed: isDisplayNameAllowed,
            uploadFile: uploadFile,
            deleteDirectory: deleteDirectory,
            getContent: getContent,
            refreshLastChangeDateTimeOfParent: refreshLastChangeDateTimeOfParent,
            calculateFolderSize: calculateFolderSize,
            moveEntry: moveEntry,
            copyEntry: copyEntry,
            getEntryByAbsolutePath: getEntryByAbsolutePath,
            renameEntry: renameEntry,
            isEntryExisting: isEntryExisting,
            getEntryById: getEntryById,
            getAbsolutePathbyId: getAbsolutePathbyId,
        };
    };

    angular.module('redundancy').factory('fileSystem', ['$http', fileSystem]);
}());